<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:penjualan.lihat', only: ['index', 'show']),
            new Middleware('permission:penjualan.tambah', only: ['create', 'store']),
            new Middleware('permission:penjualan.batal', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Sale::with('user')->withCount('items');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('dari')) {
            $query->whereDate('sale_date', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('sale_date', '<=', $request->sampai);
        }

        $sales = $query->latest('sale_date')->latest('id')->paginate(25)->withQueryString();

        $stats = [
            'omzet_hari_ini' => Sale::whereDate('sale_date', today())
                ->where('status', 'paid')->sum('total_amount'),
            'omzet_bulan_ini' => Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->where('status', 'paid')->sum('total_amount'),
            'transaksi_hari_ini' => Sale::whereDate('sale_date', today())
                ->where('status', 'paid')->count(),
        ];

        return view('sales.index', compact('sales', 'stats'));
    }

    public function create()
    {
        $invoice    = Sale::generateInvoiceNumber();
        $ppnDefault = (int) \App\Models\Setting::get('ppn_default', 0);
        $grosirSamePriceWarning = (bool) \App\Models\Setting::get('grosir_same_price_warning', true);
        return view('sales.create', compact('invoice', 'ppnDefault', 'grosirSamePriceWarning'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number'  => 'required|string|unique:sales,invoice_number',
            'sale_date'       => 'required|date',
            'price_type'      => 'required|in:eceran,grosir',
            'payment_method'  => 'required|in:cash,debit,qris',
            'tax_rate'        => 'required|numeric|min:0|max:100',
            'paid_amount'     => 'required|numeric|min:0',
            'buyer_name'      => 'nullable|string|max:150',
            'items'           => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id'    => 'required|exists:product_units,id',
            'items.*.qty'        => 'required|numeric|min:0.0001',
            'items.*.sell_price' => 'required|numeric|min:0',
        ], [
            'invoice_number.unique' => 'Nomor faktur sudah digunakan.',
            'items.required'        => 'Minimal satu item harus ditambahkan.',
        ]);

        try {
        $sale = DB::transaction(function () use ($request) {
            $total = 0;
            $itemData = [];

            foreach ($request->items as $item) {
                // lockForUpdate: cegah race condition stok bersamaan
                $product = \App\Models\Product::lockForUpdate()->findOrFail($item['product_id']);
                $unit    = \App\Models\ProductUnit::findOrFail($item['unit_id']);

                $qty      = (float) $item['qty'];
                $price    = (float) $item['sell_price'];
                $qtyBase  = $qty * (float) $unit->conversion;
                $subtotal = $qty * $price;
                $hppSnap  = (float) $product->avg_cost;
                $profit   = $subtotal - ($qtyBase * $hppSnap);

                // Validasi stok di dalam transaction (setelah lock)
                if ((float) $product->stock_qty < $qtyBase) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup. Tersedia: {$product->stock_qty} (base unit).",
                    ]);
                }

                $itemData[] = compact('unit', 'product', 'qty', 'price', 'qtyBase', 'subtotal', 'hppSnap', 'profit') + ['item' => $item];
                $total += $subtotal;
            }

            $taxRate   = (float) $request->tax_rate;
            $taxAmount = round($total * $taxRate / 100, 2);
            $grandTotal = $total + $taxAmount;

            $sale = Sale::create([
                'user_id'             => auth()->id(),
                'invoice_number'      => $request->invoice_number,
                'sale_date'           => $request->sale_date,
                'price_type'          => $request->price_type,
                'payment_method'      => $request->payment_method,
                'subtotal_before_tax' => $total,
                'tax_rate'            => $taxRate,
                'tax_amount'          => $taxAmount,
                'total_amount'        => $grandTotal,
                'paid_amount'         => $request->paid_amount,
                'change_amount'       => max(0, (float) $request->paid_amount - $grandTotal),
                'status'              => 'paid',
                'notes'               => $request->notes,
                'buyer_name'          => $request->buyer_name ?: null,
            ]);

            foreach ($itemData as $d) {
                $sale->items()->create([
                    'product_id'   => $d['item']['product_id'],
                    'unit_id'      => $d['item']['unit_id'],
                    'qty'          => $d['qty'],
                    'qty_base'     => $d['qtyBase'],
                    'sell_price'   => $d['price'],
                    'hpp_snapshot' => $d['hppSnap'],
                    'subtotal'     => $d['subtotal'],
                    'profit'       => $d['profit'],
                ]);

                $stockBefore = (float) $d['product']->stock_qty;
                $stockAfter  = $stockBefore - $d['qtyBase'];

                StockLedger::create([
                    'product_id'     => $d['product']->id,
                    'qty_base'       => $d['qtyBase'],
                    'movement_type'  => 'out',
                    'reference_type' => 'sale',
                    'reference_id'   => $sale->id,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockAfter,
                    'cost_per_unit'  => $d['hppSnap'],
                    'notes'          => "Penjualan {$sale->invoice_number}",
                ]);

                $d['product']->update(['stock_qty' => $stockAfter]);
            }

            return $sale;
        });
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Sale creation failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Transaksi gagal diproses. Silakan coba lagi.');
        }

        return redirect()->route('sales.show', [$sale, 'print' => 1])
            ->with('success', "Penjualan {$sale->invoice_number} berhasil disimpan.");
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product', 'items.unit']);
        return view('sales.show', compact('sale'));
    }

    public function apiSearch(Request $request)
    {
        $products = Product::with(['units.barcodes', 'units.prices'])
            ->where('is_active', true)
            ->where('stock_qty', '>', 0)
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('sku', 'like', "%{$request->q}%");
            })
            ->limit(10)->get();

        return response()->json($this->formatProducts($products));
    }

    public function apiBarcode(string $barcode)
    {
        $pb = ProductBarcode::with(['product.units.prices', 'unit'])
            ->where('barcode', $barcode)
            ->first();

        if (!$pb || $pb->product->stock_qty <= 0) {
            return response()->json(['message' => 'Produk tidak ditemukan atau stok habis.'], 404);
        }

        return response()->json([
            'product' => $this->formatProducts(collect([$pb->product]))[0],
            'unit'    => ['id' => $pb->unit->id, 'unit_name' => $pb->unit->unit_name],
        ]);
    }

    private function formatProducts($products): array
    {
        return $products->map(fn($p) => [
            'id'        => $p->id,
            'name'      => $p->name,
            'sku'       => $p->sku,
            'stock_qty' => (float) $p->stock_qty,
            'units'     => $p->units->map(fn($u) => [
                'id'           => $u->id,
                'unit_name'    => $u->unit_name,
                'conversion'   => (float) $u->conversion,
                'is_base'      => $u->is_base,
                'price_eceran'    => (float) ($u->prices->where('price_type','eceran')->first()?->price ?? 0),
                'price_grosir'    => (float) ($u->prices->where('price_type','grosir')->first()?->price ?? 0),
                'min_qty_grosir'  => (float) ($u->prices->where('price_type','grosir')->first()?->min_qty ?? 0),
                'stock_display'=> round((float) $p->stock_qty / max((float) $u->conversion, 0.0001), 2),
            ]),
        ])->toArray();
    }

    public function destroy(Sale $sale)
    {
        if ($sale->status !== 'paid') {
            return back()->with('error', 'Hanya penjualan berstatus Lunas yang bisa dibatalkan.');
        }

        DB::transaction(function () use ($sale) {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                $product    = $item->product;
                $stockBefore = (float) $product->stock_qty;
                $stockAfter  = $stockBefore + (float) $item->qty_base;

                StockLedger::create([
                    'product_id'     => $product->id,
                    'qty_base'       => (float) $item->qty_base,
                    'movement_type'  => 'adjustment',
                    'reference_type' => 'sale_cancel',
                    'reference_id'   => $sale->id,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockAfter,
                    'cost_per_unit'  => (float) $item->hpp_snapshot,
                    'notes'          => "Batal penjualan {$sale->invoice_number}",
                ]);

                $product->update(['stock_qty' => $stockAfter]);
            }

            $sale->update(['status' => 'cancelled']);
        });

        return back()->with('success', "Penjualan {$sale->invoice_number} berhasil dibatalkan. Stok dikembalikan.");
    }
}
