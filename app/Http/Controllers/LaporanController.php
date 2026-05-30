<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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

    // ── Laporan Keuangan ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'bulan_ini');

        [$from, $to] = $this->parsePeriode($periode, $request);

        // ── Penjualan ─────────────────────────────────────────────────────────
        $salesBase = Sale::whereBetween('sale_date', [$from, $to])->where('status', 'paid');

        $totalPenjualan    = (float) $salesBase->sum('total_amount');
        $jumlahTransaksi   = $salesBase->count();
        $totalSubtotal     = (float) $salesBase->sum('subtotal_before_tax');
        $totalPPN          = (float) $salesBase->sum('tax_amount');

        // HPP
        $totalHpp = (float) SaleItem::whereHas('sale', fn($q) =>
                $q->whereBetween('sale_date', [$from, $to])->where('status', 'paid'))
            ->selectRaw('SUM(qty_base * hpp_snapshot) as hpp')
            ->value('hpp') ?? 0;

        $labaKotor = $totalSubtotal - $totalHpp;

        // Pengeluaran
        $totalPengeluaran = (float) Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $labaBersih       = $labaKotor - $totalPengeluaran;

        // Pembelian
        $totalPembelian = (float) Purchase::whereBetween('purchase_date', [$from, $to])
            ->whereIn('status', ['confirmed', 'paid'])->sum('total_amount');

        // ── Breakdown metode pembayaran ───────────────────────────────────────
        $byPayment = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'paid')
            ->select('payment_method',
                DB::raw('COUNT(*) as trx'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('SUM(tax_amount) as ppn'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        // ── Harian ───────────────────────────────────────────────────────────
        $harian = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'paid')
            ->select(
                DB::raw('DATE(sale_date) as tgl'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('SUM(subtotal_before_tax) as subtotal'),
                DB::raw('SUM(tax_amount) as ppn'),
                DB::raw('COUNT(*) as trx')
            )
            ->groupBy('tgl')->orderBy('tgl')->get();

        $harianProfit = SaleItem::whereHas('sale', fn($q) =>
                $q->whereBetween('sale_date', [$from, $to])->where('status', 'paid'))
            ->select(DB::raw('DATE(sales.sale_date) as tgl'), DB::raw('SUM(profit) as laba'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->groupBy('tgl')->pluck('laba', 'tgl');

        $harian = $harian->map(function ($row) use ($harianProfit) {
            $row->laba = (float) ($harianProfit[$row->tgl] ?? 0);
            return $row;
        });

        // ── Top produk ────────────────────────────────────────────────────────
        $topProduk = SaleItem::whereHas('sale', fn($q) =>
                $q->whereBetween('sale_date', [$from, $to])->where('status', 'paid'))
            ->select('product_id',
                DB::raw('SUM(subtotal) as total_jual'),
                DB::raw('SUM(profit) as total_laba'),
                DB::raw('SUM(qty_base) as total_qty_base'))
            ->with('product:id,name,sku')
            ->groupBy('product_id')
            ->orderByDesc('total_jual')->limit(10)->get();

        // ── Pengeluaran per kategori ──────────────────────────────────────────
        $pengeluaranPerKategori = Expense::whereBetween('expense_date', [$from, $to])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')->orderByDesc('total')->get();

        return view('laporan.index', compact(
            'from', 'to', 'periode',
            'totalPenjualan', 'jumlahTransaksi',
            'totalSubtotal', 'totalPPN',
            'totalHpp', 'labaKotor',
            'totalPengeluaran', 'labaBersih',
            'pengeluaranPerKategori',
            'totalPembelian',
            'byPayment', 'harian', 'topProduk',
        ));
    }

    // ── Laporan Per Kasir ─────────────────────────────────────────────────────
    public function shift(Request $request)
    {
        $tanggal  = $request->get('tanggal', today()->toDateString());
        $kasirId  = $request->get('kasir_id');

        $from = Carbon::parse($tanggal)->startOfDay();
        $to   = Carbon::parse($tanggal)->endOfDay();

        // Semua kasir yang pernah transaksi pada tanggal ini
        $kasirList = \App\Models\User::whereHas('sales', fn($q) =>
                $q->whereDate('sale_date', $tanggal)->where('status', 'paid'))
            ->orderBy('name')->get();

        // Jika filter kasir tertentu
        $salesQuery = Sale::whereDate('sale_date', $tanggal)
            ->where('status', 'paid')
            ->with(['user', 'items.product', 'items.unit']);

        if ($kasirId) {
            $salesQuery->where('user_id', $kasirId);
        }

        $allSales = $salesQuery->latest('created_at')->get();

        // Group by kasir
        $byKasir = $allSales->groupBy('user_id')->map(function ($sales, $userId) {
            $user = $sales->first()->user;
            return [
                'user'       => $user,
                'sales'      => $sales,
                'trx'        => $sales->count(),
                'subtotal'   => $sales->sum('subtotal_before_tax'),
                'ppn'        => $sales->sum('tax_amount'),
                'total'      => $sales->sum('total_amount'),
                'by_payment' => $sales->groupBy('payment_method')->map(fn($g) => [
                    'trx'   => $g->count(),
                    'total' => $g->sum('total_amount'),
                ]),
            ];
        })->values();

        // Totals
        $totalPenjualan  = (float) $allSales->sum('total_amount');
        $totalSubtotal   = (float) $allSales->sum('subtotal_before_tax');
        $totalPPN        = (float) $allSales->sum('tax_amount');
        $jumlahTransaksi = $allSales->count();

        $totalHpp = (float) SaleItem::whereHas('sale', fn($q) =>
                $q->whereDate('sale_date', $tanggal)->where('status', 'paid')
                  ->when($kasirId, fn($q) => $q->where('user_id', $kasirId)))
            ->selectRaw('SUM(qty_base * hpp_snapshot) as hpp')
            ->value('hpp') ?? 0;

        $labaKotor = $totalSubtotal - $totalHpp;

        $totalPengeluaran = (float) Expense::whereDate('expense_date', $tanggal)->sum('amount');
        $labaBersih       = $labaKotor - $totalPengeluaran;

        $expenses = Expense::whereDate('expense_date', $tanggal)->with('user')->get();

        $byPaymentTotal = $allSales->groupBy('payment_method')->map(fn($g) => [
            'trx'      => $g->count(),
            'total'    => $g->sum('total_amount'),
            'subtotal' => $g->sum('subtotal_before_tax'),
            'ppn'      => $g->sum('tax_amount'),
        ]);

        $filterKasir = $kasirId ? \App\Models\User::find($kasirId) : null;

        return view('laporan.shift', compact(
            'tanggal', 'kasirId', 'kasirList', 'filterKasir',
            'byKasir', 'allSales', 'expenses',
            'totalPenjualan', 'totalSubtotal', 'totalPPN',
            'jumlahTransaksi', 'totalHpp', 'labaKotor',
            'totalPengeluaran', 'labaBersih',
            'byPaymentTotal',
        ));
    }

    // ── Helper ────────────────────────────────────────────────────────────────
    private function parsePeriode(string $periode, Request $request): array
    {
        return match($periode) {
            'hari_ini'   => [today(), today()],
            'minggu_ini' => [now()->startOfWeek(), now()->endOfWeek()],
            'bulan_lalu' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'tahun_ini'  => [now()->startOfYear(), now()->endOfYear()],
            'kustom'     => [
                Carbon::parse($request->get('dari', now()->startOfMonth())),
                Carbon::parse($request->get('sampai', now()->endOfMonth())),
            ],
            default      => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}
