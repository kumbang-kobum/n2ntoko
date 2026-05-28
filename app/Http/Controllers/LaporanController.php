<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:laporan.lihat'),
        ];
    }

    public function index(Request $request)
    {
        // ── Rentang tanggal ──────────────────────────────────────────────────
        $periode = $request->get('periode', 'bulan_ini');

        [$from, $to] = match($periode) {
            'hari_ini'    => [today(), today()],
            'minggu_ini'  => [now()->startOfWeek(), now()->endOfWeek()],
            'bulan_lalu'  => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'tahun_ini'   => [now()->startOfYear(), now()->endOfYear()],
            'kustom'      => [
                Carbon::parse($request->get('dari', now()->startOfMonth())),
                Carbon::parse($request->get('sampai', now()->endOfMonth())),
            ],
            default       => [now()->startOfMonth(), now()->endOfMonth()], // bulan_ini
        };

        // ── Ringkasan penjualan ──────────────────────────────────────────────
        $salesQuery = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'paid')
            ->withTrashed(false);

        $totalPenjualan = (float) $salesQuery->sum('total_amount');
        $jumlahTransaksi = $salesQuery->count();

        $totalHpp = (float) SaleItem::whereHas('sale', fn($q) =>
                $q->whereBetween('sale_date', [$from, $to])
                  ->where('status', 'paid'))
            ->selectRaw('SUM(qty_base * hpp_snapshot) as hpp')
            ->value('hpp') ?? 0;

        $labaKotor = $totalPenjualan - $totalHpp;

        // ── Pembelian di periode (barang diterima) ───────────────────────────
        $totalPembelian = (float) Purchase::whereBetween('purchase_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'paid'])
            ->sum('total_amount');

        // ── Harian ──────────────────────────────────────────────────────────
        $harian = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'paid')
            ->select(
                DB::raw('DATE(sale_date) as tgl'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as trx')
            )
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $harianProfit = SaleItem::whereHas('sale', fn($q) =>
                $q->whereBetween('sale_date', [$from, $to])
                  ->where('status', 'paid'))
            ->select(
                DB::raw('DATE(sales.sale_date) as tgl'),
                DB::raw('SUM(profit) as laba')
            )
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->groupBy('tgl')
            ->pluck('laba', 'tgl');

        $harian = $harian->map(function ($row) use ($harianProfit) {
            $row->laba = (float) ($harianProfit[$row->tgl] ?? 0);
            return $row;
        });

        // ── Top 10 produk ────────────────────────────────────────────────────
        $topProduk = SaleItem::whereHas('sale', fn($q) =>
                $q->whereBetween('sale_date', [$from, $to])
                  ->where('status', 'paid'))
            ->select(
                'product_id',
                DB::raw('SUM(subtotal) as total_jual'),
                DB::raw('SUM(profit) as total_laba'),
                DB::raw('SUM(qty_base) as total_qty_base')
            )
            ->with('product:id,name,sku')
            ->groupBy('product_id')
            ->orderByDesc('total_jual')
            ->limit(10)
            ->get();

        return view('laporan.index', compact(
            'from', 'to', 'periode',
            'totalPenjualan', 'jumlahTransaksi',
            'totalHpp', 'labaKotor',
            'totalPembelian',
            'harian', 'topProduk',
        ));
    }
}
