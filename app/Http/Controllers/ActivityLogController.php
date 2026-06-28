<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:activity-log.lihat'),
            new Middleware('permission:activity-log.hapus', only: ['destroy', 'destroyAll']),
        ];
    }

    public function index(Request $request)
    {
        $modelLabels = [
            'App\\Models\\Product'      => 'Produk',
            'App\\Models\\Sale'         => 'Penjualan',
            'App\\Models\\Purchase'     => 'Pembelian',
            'App\\Models\\Customer'     => 'Pelanggan',
            'App\\Models\\Supplier'     => 'Supplier',
            'App\\Models\\Category'     => 'Kategori',
            'App\\Models\\Expense'      => 'Pengeluaran',
            'App\\Models\\Employee'     => 'Karyawan',
            'App\\Models\\Attendance'   => 'Absensi',
            'App\\Models\\ProductPrice' => 'Harga Produk',
            'App\\Models\\Setting'      => 'Pengaturan',
            'App\\Models\\User'         => 'Pengguna',
        ];

        $query = Activity::with('causer')
            ->latest()
            ->select('activity_log.*');

        if ($request->filled('model')) {
            $query->where('subject_type', $request->model);
        }

        if ($request->filled('event')) {
            if ($request->event === 'stok_opname') {
                $query->where('log_name', 'stok_opname');
            } else {
                $query->where('event', $request->event)
                      ->where('log_name', '!=', 'stok_opname');
            }
        }

        if ($request->filled('user')) {
            $query->where(function ($q) use ($request) {
                $q->whereHasMorph('causer', '*', fn($q2) =>
                    $q2->where('name', 'like', '%' . $request->user . '%')
                )->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.causer_name')) LIKE ?",
                    ['%' . $request->user . '%']);
            });
        }

        if ($request->filled('nama')) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.subject_name')) LIKE ?",
                ['%' . $request->nama . '%']);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $logs = $query->paginate(50)->withQueryString();

        return view('activity_log.index', compact('logs', 'modelLabels'));
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return back()->with('success', 'Entri log berhasil dihapus.');
    }

    public function destroyAll(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('model')) {
            $query->where('subject_type', $request->model);
        }
        if ($request->filled('event')) {
            if ($request->event === 'stok_opname') {
                $query->where('log_name', 'stok_opname');
            } else {
                $query->where('event', $request->event)
                      ->where('log_name', '!=', 'stok_opname');
            }
        }
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $count = $query->count();
        $query->delete();

        return back()->with('success', "{$count} entri log berhasil dihapus.");
    }
}
