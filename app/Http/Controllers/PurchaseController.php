<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Purchase;
use App\Models\StockLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pembelian.lihat', only: ['index', 'show']),
            new Middleware('permission:pembelian.tambah', only: ['create', 'store']),
            new Middleware('permission:pembelian.edit', only: ['edit', 'update', 'order', 'pay']),
            new Middleware('permission:pembelian.konfirmasi', only: ['confirm']),
            new Middleware('permission:pembelian.hapus', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user'])
            ->withCount('items');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('supplier', fn($q) => $q->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari')) {
            $query->whereDate('purchase_date', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('purchase_date', '<=', $request->sampai);
        }

        $purchases = $query->latest('purchase_date')->latest('id')->paginate(20)->withQueryString();

        $stats = [
            'total_bulan_ini' => Purchase::whereMonth('purchase_date', now()->month)
                ->whereYear('purchase_date', now()->year)
                ->where('status', '!=', 'draft')
                ->sum('total_amount'),
            'hutang' => Purchase::where('status', 'confirmed')
                ->selectRaw('SUM(total_amount - paid_amount) as total')
                ->value('total') ?? 0,
            'draft'  => Purchase::where('status', 'draft')->count(),
        ];

        return view('purchases.index', compact('purchases', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $invoice   = Purchase::generateInvoiceNumber();
        return view('purchases.create', compact('suppliers', 'invoice'));
    }

    public function store(Request $request)
    {
        $this->validatePurchase($request);

        try {
            $purchase = DB::transaction(function () use ($request) {
                $purchase = Purchase::create([
                    'supplier_id'    => $request->supplier_id ?: null,
                    'user_id'        => auth()->id(),
                    'invoice_number' => $request->invoice_number,
                    'purchase_date'  => $request->purchase_date,
                    'notes'          => $request->notes,
                    'buyer_name'     => $request->buyer_name ?: null,
                    'status'         => 'draft',
                    'total_amount'   => 0,
                    'paid_amount'    => 0,
                ]);

                $total = $this->saveItems($purchase, $request->items);
                $purchase->update(['total_amount' => $total]);

                if ($request->action === 'confirm') {
                    $this->confirmPurchase($purchase);
                }

                return $purchase;
            });
        } catch (\Exception $e) {
            \Log::error('Purchase creation failed', ['user_id' => auth()->id(), 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Pembelian gagal disimpan. Silakan coba lagi.');
        }

        $msg = $request->action === 'confirm'
            ? "Pembelian {$purchase->invoice_number} dikonfirmasi. Stok telah diperbarui."
            : "Pembelian {$purchase->invoice_number} disimpan sebagai draft.";

        return redirect()->route('purchases.show', $purchase)->with('success', $msg);
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product', 'items.unit']);
        return view('purchases.show', compact('purchase'));
    }

    public function order(Purchase $purchase)
    {
        if (!$purchase->isDraft()) {
            return back()->with('error', 'Hanya draft yang bisa diubah ke status Dipesan.');
        }

        $purchase->update(['status' => 'ordered']);

        return back()->with('success', "PO {$purchase->invoice_number} ditandai sebagai Dipesan. Stok belum berubah.");
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            return back()->with('error', 'Hanya pembelian berstatus Draft yang bisa diedit.');
        }

        $purchase->load(['items.product', 'items.unit.product']);
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('purchases.edit', compact('purchase', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            return back()->with('error', 'Hanya pembelian berstatus Draft yang bisa diedit.');
        }

        $this->validatePurchase($request, $purchase->id);

        $purchase = DB::transaction(function () use ($request, $purchase) {
            $purchase->items()->delete();

            $purchase->update([
                'supplier_id'   => $request->supplier_id ?: null,
                'invoice_number'=> $request->invoice_number,
                'purchase_date' => $request->purchase_date,
                'notes'         => $request->notes,
                'buyer_name'    => $request->buyer_name ?: null,
            ]);

            $total = $this->saveItems($purchase, $request->items);
            $purchase->update(['total_amount' => $total]);

            if ($request->action === 'confirm') {
                $this->confirmPurchase($purchase);
            }

            return $purchase;
        });

        $msg = $request->action === 'confirm'
            ? "Pembelian {$purchase->invoice_number} dikonfirmasi. Stok telah diperbarui."
            : "Pembelian {$purchase->invoice_number} berhasil diperbarui.";

        return redirect()->route('purchases.show', $purchase)->with('success', $msg);
    }

    public function confirm(Purchase $purchase)
    {
        if ($purchase->isConfirmed()) {
            return back()->with('error', 'Pembelian ini sudah diterima.');
        }

        if (!$purchase->isDraft() && !$purchase->isOrdered()) {
            return back()->with('error', 'Status pembelian tidak valid untuk dikonfirmasi.');
        }

        DB::transaction(fn() => $this->confirmPurchase($purchase));

        return back()->with('success', "Pembelian {$purchase->invoice_number} dikonfirmasi. Stok telah diperbarui.");
    }

    public function pay(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'confirmed') {
            return back()->with('error', 'Hanya pembelian berstatus Diterima yang bisa dibayar.');
        }

        $request->validate([
            'bayar' => ['required', 'numeric', 'min:1', 'max:' . $purchase->hutang],
        ], [
            'bayar.required' => 'Jumlah bayar wajib diisi.',
            'bayar.max'      => 'Jumlah bayar melebihi sisa hutang.',
        ]);

        $newPaid  = (float) $purchase->paid_amount + (float) $request->bayar;
        $lunas    = $newPaid >= (float) $purchase->total_amount;

        $purchase->update([
            'paid_amount' => $newPaid,
            'status'      => $lunas ? 'paid' : 'confirmed',
        ]);

        $msg = $lunas
            ? "Pembelian {$purchase->invoice_number} telah LUNAS."
            : "Pembayaran Rp " . number_format($request->bayar, 0, ',', '.') . " berhasil dicatat. Sisa hutang: Rp " . number_format($purchase->total_amount - $newPaid, 0, ',', '.') . ".";

        return back()->with('success', $msg);
    }

    public function destroy(Purchase $purchase)
    {
        if (!in_array($purchase->status, ['draft', 'ordered'])) {
            return back()->with('error', 'Hanya pembelian Draft/Dipesan yang bisa dihapus.');
        }

        $invoice = $purchase->invoice_number;
        $purchase->items()->delete();
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', "Pembelian {$invoice} berhasil dihapus.");
    }

    // ── API: cari produk untuk form ───────────────────────────────────────────
    public function apiSearch(Request $request)
    {
        $products = Product::with(['units.barcodes', 'units.prices'])
            ->where('is_active', true)
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('sku', 'like', "%{$request->q}%");
            })
            ->limit(10)->get();

        return response()->json($products->map(fn($p) => [
            'id'   => $p->id,
            'name' => $p->name,
            'sku'  => $p->sku,
            'units'=> $p->units->map(fn($u) => [
                'id'         => $u->id,
                'unit_name'  => $u->unit_name,
                'conversion' => (float) $u->conversion,
                'is_base'    => $u->is_base,
                'last_price' => $u->prices->where('price_type','eceran')->first()?->price ?? 0,
            ]),
        ]));
    }

    public function apiBarcode(string $barcode)
    {
        $pb = ProductBarcode::with(['product.units.prices', 'unit'])
            ->where('barcode', $barcode)
            ->first();

        if (!$pb) {
            return response()->json(['message' => 'Barcode tidak ditemukan.'], 404);
        }

        return response()->json([
            'product' => [
                'id'   => $pb->product->id,
                'name' => $pb->product->name,
                'sku'  => $pb->product->sku,
                'units'=> $pb->product->units->map(fn($u) => [
                    'id'         => $u->id,
                    'unit_name'  => $u->unit_name,
                    'conversion' => (float) $u->conversion,
                    'is_base'    => $u->is_base,
                    'last_price' => $u->prices->where('price_type','eceran')->first()?->price ?? 0,
                ]),
            ],
            'unit' => [
                'id'        => $pb->unit->id,
                'unit_name' => $pb->unit->unit_name,
            ],
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function validatePurchase(Request $request, ?int $excludeId = null): void
    {
        $invoiceRule = $excludeId
            ? "required|string|max:100|unique:purchases,invoice_number,{$excludeId}"
            : 'required|string|max:100|unique:purchases,invoice_number';

        $request->validate([
            'invoice_number'         => $invoiceRule,
            'purchase_date'          => 'required|date',
            'supplier_id'            => 'nullable|exists:suppliers,id',
            'buyer_name'             => 'nullable|string|max:150',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.unit_id'        => 'required|exists:product_units,id',
            'items.*.qty'            => 'required|numeric|min:0.0001',
            'items.*.buy_price'      => 'required|numeric|min:0',
        ], [
            'invoice_number.required' => 'Nomor faktur wajib diisi.',
            'invoice_number.unique'   => 'Nomor faktur sudah digunakan.',
            'purchase_date.required'  => 'Tanggal pembelian wajib diisi.',
            'items.required'          => 'Minimal satu item harus ditambahkan.',
            'items.*.product_id.required' => 'Pilih produk untuk setiap baris.',
            'items.*.qty.required'    => 'Qty wajib diisi.',
            'items.*.buy_price.required' => 'Harga beli wajib diisi.',
        ]);
    }

    private function saveItems(Purchase $purchase, array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $unit     = \App\Models\ProductUnit::find($item['unit_id']);
            $qtyBase  = (float) $item['qty'] * (float) $unit->conversion;
            $priceBase = (float) $item['buy_price'] / (float) $unit->conversion;
            $subtotal  = (float) $item['qty'] * (float) $item['buy_price'];

            $purchase->items()->create([
                'product_id'     => $item['product_id'],
                'unit_id'        => $item['unit_id'],
                'qty'            => $item['qty'],
                'qty_base'       => $qtyBase,
                'buy_price'      => $item['buy_price'],
                'buy_price_base' => $priceBase,
                'subtotal'       => $subtotal,
            ]);

            $total += $subtotal;
        }

        return $total;
    }

    private function confirmPurchase(Purchase $purchase): void
    {
        $purchase->load('items');

        foreach ($purchase->items as $item) {
            // lockForUpdate: cegah race condition saat konfirmasi bersamaan
            $product = \App\Models\Product::lockForUpdate()->findOrFail($item->product_id);

            // Hitung avg_cost baru (moving average)
            $currentStock = (float) $product->stock_qty;
            $currentAvg   = (float) $product->avg_cost;
            $inQty        = (float) $item->qty_base;
            $inPrice      = (float) $item->buy_price_base;

            $newAvg = ($currentStock + $inQty) > 0
                ? (($currentStock * $currentAvg) + ($inQty * $inPrice)) / ($currentStock + $inQty)
                : $inPrice;

            $stockAfter = $currentStock + $inQty;

            // Catat di stock_ledger
            StockLedger::create([
                'product_id'     => $product->id,
                'qty_base'       => $inQty,
                'movement_type'  => 'in',
                'reference_type' => 'purchase',
                'reference_id'   => $purchase->id,
                'stock_before'   => $currentStock,
                'stock_after'    => $stockAfter,
                'cost_per_unit'  => $inPrice,
                'notes'          => "Pembelian {$purchase->invoice_number}",
            ]);

            // Update produk
            $product->update([
                'stock_qty' => $stockAfter,
                'avg_cost'  => round($newAvg, 4),
            ]);
        }

        $purchase->update(['status' => 'confirmed']);
    }
}
