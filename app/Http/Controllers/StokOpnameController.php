<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:produk.edit'),
        ];
    }

    public function index()
    {
        $products = Product::with('baseUnit', 'category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('stok_opname.index', compact('products'));
    }

    public function cetak()
    {
        $products = Product::with('baseUnit', 'category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $settings = DB::table('settings')->pluck('value', 'key');

        return view('stok_opname.cetak', compact('products', 'settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustments'              => 'required|array',
            'adjustments.*.product_id' => 'required|exists:products,id',
            'adjustments.*.qty_fisik'  => 'nullable|numeric|min:0',
            'adjustments.*.notes'      => 'nullable|string|max:255',
        ]);

        $changed = 0;

        DB::transaction(function () use ($request, &$changed) {
            foreach ($request->input('adjustments') as $adj) {
                // Skip if qty_fisik is not provided
                if ($adj['qty_fisik'] === null || $adj['qty_fisik'] === '') {
                    continue;
                }

                $product  = Product::lockForUpdate()->find($adj['product_id']);
                if (!$product) continue;

                $qtyFisik = (float) $adj['qty_fisik'];
                $selisih  = $qtyFisik - (float) $product->stock_qty;

                if (abs($selisih) < 0.0001) {
                    continue; // tidak ada perubahan
                }

                StockLedger::create([
                    'product_id'      => $product->id,
                    'qty_base'        => $selisih,
                    'movement_type'   => 'adjustment',
                    'reference_type'  => 'stok_opname',
                    'reference_id'    => null,
                    'stock_before'    => $product->stock_qty,
                    'stock_after'     => $qtyFisik,
                    'cost_per_unit'   => $product->avg_cost,
                    'notes'           => $adj['notes'] ?? 'Stok opname',
                ]);

                $product->update(['stock_qty' => $qtyFisik]);
                $changed++;
            }
        });

        return redirect()->route('stok-opname.index')
            ->with('success', $changed > 0
                ? "{$changed} produk berhasil disesuaikan."
                : 'Tidak ada perubahan stok.');
    }
}
