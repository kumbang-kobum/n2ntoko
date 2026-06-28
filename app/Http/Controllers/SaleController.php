<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:penjualan.lihat', only: ['index', 'show']),
            new Middleware('permission:penjualan.tambah', only: ['create', 'store']),
            new Middleware('permission:penjualan.edit', only: ['edit', 'update']),
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

    public function edit(Sale $sale)
    {
        if ($sale->status !== 'paid') {
            return redirect()->route('sales.show', $sale)
                ->with('error', 'Penjualan yang sudah dibatalkan tidak bisa diedit.');
        }

        $sale->load(['items.product.units.prices', 'items.unit']);

        $invoice    = $sale->invoice_number;
        $ppnDefault = (float) $sale->tax_rate;
        $grosirSamePriceWarning = (bool) \App\Models\Setting::get('grosir_same_price_warning', true);
        $initialData = $this->buildSaleInitialData($sale);

        return view('sales.create', compact('sale', 'invoice', 'ppnDefault', 'grosirSamePriceWarning', 'initialData'));
    }

    public function store(Request $request)
    {
        $this->validateSaleRequest($request);

        try {
            $sale = DB::transaction(function () use ($request) {
                [$itemData, $total] = $this->prepareSaleItemData($request->items);
                $totals = $this->calculateSaleTotals($total, (float) $request->tax_rate);
                $paidAmount = $this->resolvePaidAmount($request->payment_method, (float) $request->paid_amount, $totals['grandTotal']);

                $sale = Sale::create([
                    'user_id'             => auth()->id(),
                    'invoice_number'      => $request->invoice_number,
                    'sale_date'           => $request->sale_date,
                    'price_type'          => $request->price_type,
                    'payment_method'      => $request->payment_method,
                    'subtotal_before_tax' => $total,
                    'tax_rate'            => $totals['taxRate'],
                    'tax_amount'          => $totals['taxAmount'],
                    'total_amount'        => $totals['grandTotal'],
                    'paid_amount'         => $paidAmount,
                    'change_amount'       => max(0, $paidAmount - $totals['grandTotal']),
                    'status'              => 'paid',
                    'notes'               => $request->notes,
                    'buyer_name'          => $request->buyer_name ?: null,
                ]);

                $this->writeSaleItemsAndStock($sale, $itemData, 'sale', "Penjualan {$sale->invoice_number}");

                return $sale;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Sale creation failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Transaksi gagal diproses. Silakan coba lagi.');
        }

        return redirect()->route('sales.show', [$sale, 'cetak' => 1])
            ->with('success', "Penjualan {$sale->invoice_number} berhasil disimpan.");
    }

    public function update(Request $request, Sale $sale)
    {
        $this->validateSaleRequest($request, $sale);

        try {
            $sale = DB::transaction(function () use ($request, $sale) {
                $sale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

                if ($sale->status !== 'paid') {
                    throw ValidationException::withMessages([
                        'status' => 'Penjualan yang sudah dibatalkan tidak bisa diedit.',
                    ]);
                }

                $this->restoreSaleStockForEdit($sale);
                $sale->items()->delete();

                [$itemData, $total] = $this->prepareSaleItemData($request->items);
                $totals = $this->calculateSaleTotals($total, (float) $request->tax_rate);
                $paidAmount = $this->resolvePaidAmount($request->payment_method, (float) $request->paid_amount, $totals['grandTotal']);

                $sale->update([
                    'invoice_number'      => $request->invoice_number,
                    'sale_date'           => $request->sale_date,
                    'price_type'          => $request->price_type,
                    'payment_method'      => $request->payment_method,
                    'subtotal_before_tax' => $total,
                    'tax_rate'            => $totals['taxRate'],
                    'tax_amount'          => $totals['taxAmount'],
                    'total_amount'        => $totals['grandTotal'],
                    'paid_amount'         => $paidAmount,
                    'change_amount'       => max(0, $paidAmount - $totals['grandTotal']),
                    'notes'               => $request->notes,
                    'buyer_name'          => $request->buyer_name ?: null,
                ]);

                $this->writeSaleItemsAndStock($sale, $itemData, 'sale_edit', "Edit penjualan {$sale->invoice_number}");

                return $sale;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Sale update failed', [
                'sale_id' => $sale->id,
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return back()->withInput()->with('error', 'Transaksi gagal diperbarui. Silakan coba lagi.');
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', "Penjualan {$sale->invoice_number} berhasil diperbarui. Stok dan total transaksi sudah disesuaikan.");
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product', 'items.unit']);
        return view('sales.show', compact('sale'));
    }

    private function validateSaleRequest(Request $request, ?Sale $sale = null): void
    {
        $invoiceRule = Rule::unique('sales', 'invoice_number');
        if ($sale) {
            $invoiceRule->ignore($sale->id);
        }

        $request->validate([
            'invoice_number'     => ['required', 'string', $invoiceRule],
            'sale_date'          => ['required', 'date'],
            'price_type'         => ['required', Rule::in(['eceran', 'grosir'])],
            'payment_method'     => ['required', Rule::in(['cash', 'debit', 'qris'])],
            'tax_rate'           => ['required', 'numeric', 'min:0', 'max:100'],
            'paid_amount'        => ['required', 'numeric', 'min:0'],
            'buyer_name'         => ['nullable', 'string', 'max:150'],
            'notes'              => ['nullable', 'string'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.unit_id'    => ['required', 'exists:product_units,id'],
            'items.*.qty'        => ['required', 'numeric', 'min:0.0001'],
            'items.*.sell_price' => ['required', 'numeric', 'min:0'],
        ], [
            'invoice_number.unique' => 'Nomor faktur sudah digunakan.',
            'items.required'        => 'Minimal satu item harus ditambahkan.',
        ]);
    }

    private function prepareSaleItemData(array $items): array
    {
        $total = 0;
        $itemData = [];
        $reservedByProduct = [];

        foreach ($items as $item) {
            $product = Product::lockForUpdate()->findOrFail($item['product_id']);
            $unit = ProductUnit::where('product_id', $product->id)->findOrFail($item['unit_id']);

            $qty = (float) $item['qty'];
            $price = (float) $item['sell_price'];
            $conversion = (float) $unit->conversion;

            if ($conversion <= 0) {
                throw ValidationException::withMessages([
                    'items' => "Konversi satuan {$unit->unit_name} untuk {$product->name} tidak valid.",
                ]);
            }

            $qtyBase = $qty * $conversion;
            $subtotal = $qty * $price;
            $hppSnap = (float) $product->avg_cost;
            $profit = $subtotal - ($qtyBase * $hppSnap);
            $reserved = (float) ($reservedByProduct[$product->id] ?? 0);
            $available = (float) $product->stock_qty - $reserved;

            if ($available < $qtyBase) {
                throw ValidationException::withMessages([
                    'items' => "Stok {$product->name} tidak cukup. Tersedia: {$available} (base unit).",
                ]);
            }

            $reservedByProduct[$product->id] = $reserved + $qtyBase;
            $itemData[] = compact('unit', 'product', 'qty', 'price', 'qtyBase', 'subtotal', 'hppSnap', 'profit') + ['item' => $item];
            $total += $subtotal;
        }

        return [$itemData, $total];
    }

    private function calculateSaleTotals(float $subtotal, float $taxRate): array
    {
        $taxRate = max(0, min(100, $taxRate));
        $taxAmount = round($subtotal * $taxRate / 100, 2);

        return [
            'taxRate'    => $taxRate,
            'taxAmount'  => $taxAmount,
            'grandTotal' => $subtotal + $taxAmount,
        ];
    }

    private function resolvePaidAmount(string $paymentMethod, float $paidAmount, float $grandTotal): float
    {
        if ($paymentMethod !== 'cash') {
            return $grandTotal;
        }

        if ($paidAmount < $grandTotal) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Uang diterima kurang dari total bayar.',
            ]);
        }

        return $paidAmount;
    }

    private function writeSaleItemsAndStock(Sale $sale, array $itemData, string $referenceType, string $notes): void
    {
        foreach ($itemData as $d) {
            $product = Product::whereKey($d['product']->id)->lockForUpdate()->firstOrFail();

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

            $stockBefore = (float) $product->stock_qty;
            $stockAfter = $stockBefore - $d['qtyBase'];

            StockLedger::create([
                'product_id'     => $product->id,
                'qty_base'       => $d['qtyBase'],
                'movement_type'  => 'out',
                'reference_type' => $referenceType,
                'reference_id'   => $sale->id,
                'stock_before'   => $stockBefore,
                'stock_after'    => $stockAfter,
                'cost_per_unit'  => $d['hppSnap'],
                'notes'          => $notes,
            ]);

            $product->update(['stock_qty' => $stockAfter]);
        }
    }

    private function restoreSaleStockForEdit(Sale $sale): void
    {
        $sale->load('items');

        foreach ($sale->items as $item) {
            $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
            $stockBefore = (float) $product->stock_qty;
            $stockAfter = $stockBefore + (float) $item->qty_base;

            StockLedger::create([
                'product_id'     => $product->id,
                'qty_base'       => (float) $item->qty_base,
                'movement_type'  => 'adjustment',
                'reference_type' => 'sale_edit',
                'reference_id'   => $sale->id,
                'stock_before'   => $stockBefore,
                'stock_after'    => $stockAfter,
                'cost_per_unit'  => (float) $item->hpp_snapshot,
                'notes'          => "Edit penjualan {$sale->invoice_number} - stok lama dikembalikan",
            ]);

            $product->update(['stock_qty' => $stockAfter]);
        }
    }

    private function buildSaleInitialData(Sale $sale): array
    {
        $saleQtyByProduct = $sale->items
            ->groupBy('product_id')
            ->map(fn($items) => (float) $items->sum('qty_base'));

        return [
            'price_type'     => $sale->price_type,
            'payment_method' => $sale->payment_method,
            'tax_rate'       => (float) $sale->tax_rate,
            'paid_amount'    => (float) $sale->paid_amount,
            'items'          => $sale->items->values()->map(function (SaleItem $item) use ($saleQtyByProduct) {
                $product = $item->product;
                $availableBase = (float) $product->stock_qty + (float) ($saleQtyByProduct[$product->id] ?? 0);
                $selectedUnit = $item->unit;
                $selectedConversion = max((float) ($selectedUnit?->conversion ?? 1), 0.0001);

                return [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'sku'           => $product->sku,
                    'unit_id'       => (string) $item->unit_id,
                    'unit_name'     => $selectedUnit?->unit_name,
                    'stock_qty'     => $availableBase,
                    'stock_display' => round($availableBase / $selectedConversion, 2),
                    'qty'           => (float) $item->qty,
                    'sell_price'    => (float) $item->sell_price,
                    'subtotal'      => (float) $item->subtotal,
                    'units'         => $product->units->sortBy('conversion')->values()->map(fn($u) => [
                        'id'              => $u->id,
                        'unit_name'       => $u->unit_name,
                        'conversion'      => (float) $u->conversion,
                        'is_base'         => $u->is_base,
                        'price_eceran'    => (float) ($u->prices->where('price_type', 'eceran')->first()?->price ?? 0),
                        'price_grosir'    => (float) ($u->prices->where('price_type', 'grosir')->first()?->price ?? 0),
                        'min_qty_grosir'  => (float) ($u->prices->where('price_type', 'grosir')->first()?->min_qty ?? 0),
                        'stock_display'   => round($availableBase / max((float) $u->conversion, 0.0001), 2),
                    ])->values()->toArray(),
                ];
            })->toArray(),
        ];
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
            'units'     => $p->units->sortBy('conversion')->values()->map(fn($u) => [
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
        $invoiceNumber = $sale->invoice_number;

        try {
            DB::transaction(function () use ($sale) {
                $sale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

                if ($sale->status !== 'paid') {
                    throw new \Exception('Hanya penjualan berstatus Lunas yang bisa dibatalkan.');
                }

                foreach ($sale->items()->get() as $item) {
                    $product     = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
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
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Penjualan {$invoiceNumber} berhasil dibatalkan. Stok dikembalikan.");
    }
}
