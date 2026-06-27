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
            new Middleware('permission:stok.opname'),
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
        $user    = auth()->user();

        DB::transaction(function () use ($request, $user, &$changed) {
            foreach ($request->input('adjustments') as $adj) {
                $qtyFisikRaw = $adj['qty_fisik'] ?? null;
                if ($qtyFisikRaw === null || $qtyFisikRaw === '') {
                    continue;
                }

                $product  = Product::lockForUpdate()->find($adj['product_id']);
                if (!$product) continue;

                $qtyFisik   = (float) $qtyFisikRaw;
                $qtySebelum = (float) $product->stock_qty;
                $selisih    = $qtyFisik - $qtySebelum;

                if (abs($selisih) < 0.0001) {
                    continue;
                }

                StockLedger::create([
                    'product_id'      => $product->id,
                    'qty_base'        => $selisih,
                    'movement_type'   => 'adjustment',
                    'reference_type'  => 'stok_opname',
                    'reference_id'    => null,
                    'stock_before'    => $qtySebelum,
                    'stock_after'     => $qtyFisik,
                    'cost_per_unit'   => $product->avg_cost,
                    'notes'           => $adj['notes'] ?? 'Stok opname',
                ]);

                // Update stok tanpa memicu auto-log model (agar tidak duplikat)
                $product->disableLogging();
                $product->update(['stock_qty' => $qtyFisik]);
                $product->enableLogging();

                // Log manual dengan keterangan lengkap
                activity('stok_opname')
                    ->causedBy($user)
                    ->performedOn($product)
                    ->event('stok_opname')
                    ->withProperties([
                        'causer_name'  => $user?->name ?? 'Sistem',
                        'subject_name' => $product->name,
                        'qty_sebelum'  => $qtySebelum,
                        'qty_fisik'    => $qtyFisik,
                        'selisih'      => $selisih,
                        'catatan'      => $adj['notes'] ?? '',
                    ])
                    ->log('Stok opname: ' . $product->name);

                $changed++;
            }
        });

        return redirect()->route('stok-opname.index')
            ->with('success', $changed > 0
                ? "{$changed} produk berhasil disesuaikan."
                : 'Tidak ada perubahan stok.');
    }
}
