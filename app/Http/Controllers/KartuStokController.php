<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class KartuStokController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:produk.lihat'),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::with('baseUnit', 'category')
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('sku',  'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('kartu_stok.index', compact('products', 'search'));
    }

    public function cetakKosong()
    {
        $settings = DB::table('settings')->pluck('value', 'key');
        return view('kartu_stok.cetak_kosong', compact('settings'));
    }

    public function cetak(Request $request, Product $product)
    {
        $product->load('baseUnit', 'category');

        $dari   = $request->dari;
        $sampai = $request->sampai;

        $ledgers = StockLedger::where('product_id', $product->id)
            ->when($dari,   fn($q) => $q->whereDate('created_at', '>=', $dari))
            ->when($sampai, fn($q) => $q->whereDate('created_at', '<=', $sampai))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $settings = DB::table('settings')->pluck('value', 'key');

        return view('kartu_stok.cetak', compact('product', 'ledgers', 'dari', 'sampai', 'settings'));
    }

    public function show(Request $request, Product $product)
    {
        $product->load('baseUnit', 'category');

        $dari   = $request->dari;
        $sampai = $request->sampai;

        $ledgers = StockLedger::where('product_id', $product->id)
            ->when($dari,   fn($q) => $q->whereDate('created_at', '>=', $dari))
            ->when($sampai, fn($q) => $q->whereDate('created_at', '<=', $sampai))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $settings = DB::table('settings')->pluck('value', 'key');

        return view('kartu_stok.show', compact('product', 'ledgers', 'dari', 'sampai', 'settings'));
    }
}
