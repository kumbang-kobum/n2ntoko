<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AttendanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', only: ['admin', 'store', 'update', 'destroy', 'rekap', 'exportCsv']),
            new Middleware('permission:absensi.lihat',   only: ['admin', 'rekap']),
            new Middleware('permission:absensi.tambah',  only: ['store', 'update', 'destroy']),
            new Middleware('permission:absensi.rekap',   only: ['rekap', 'exportCsv']),
        ];
    }

    // ── Public: halaman clock in/out (tanpa login) ────────────────────────────

    public function publicPage(Request $request)
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get(['id', 'name', 'jabatan']);
        $today     = today()->toDateString();

        // Ambil absensi hari ini untuk semua pegawai aktif
        $todayAttendances = Attendance::whereDate('date', $today)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()->keyBy('employee_id');

        return view('attendances.public', compact('employees', 'today', 'todayAttendances'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'photo'       => 'nullable|string|max:1500000', // ~1 MB gambar
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $today    = today()->toDateString();
        $now      = now()->format('H:i:s');

        $attendance = Attendance::firstOrNew(['employee_id' => $employee->id, 'date' => $today]);

        if ($attendance->check_in) {
            return back()->with('error', "'{$employee->name}' sudah check-in pukul " . substr($attendance->check_in, 0, 5) . '.');
        }

        $photoPath = $this->savePhoto($request->photo, $employee->id, $today, 'in');

        $attendance->fill([
            'check_in' => $now,
            'status'   => 'hadir',
            'input_by' => 'self',
            'photo_in' => $photoPath,
        ])->save();

        return back()->with('success', "✓ {$employee->name} berhasil check-in pukul " . substr($now, 0, 5) . '.');
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'photo'       => 'nullable|string|max:1500000',
        ]);

        $employee   = Employee::findOrFail($request->employee_id);
        $today      = today()->toDateString();
        $now        = now()->format('H:i:s');

        $attendance = Attendance::where('employee_id', $employee->id)->whereDate('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            return back()->with('error', "'{$employee->name}' belum check-in hari ini.");
        }

        if ($attendance->check_out) {
            return back()->with('error', "'{$employee->name}' sudah check-out pukul " . substr($attendance->check_out, 0, 5) . '.');
        }

        $photoPath = $this->savePhoto($request->photo, $employee->id, $today, 'out');

        $attendance->update([
            'check_out' => $now,
            'input_by'  => 'self',
            'photo_out' => $photoPath,
        ]);

        return back()->with('success', "✓ {$employee->name} berhasil check-out pukul " . substr($now, 0, 5) . '.');
    }

    private function savePhoto(?string $base64, int $employeeId, string $date, string $type): ?string
    {
        if (!$base64 || !str_contains($base64, 'base64,')) {
            return null;
        }

        try {
            $raw     = substr($base64, strpos($base64, 'base64,') + 7);
            $decoded = base64_decode($raw, strict: true);

            // Tolak jika bukan binary valid
            if ($decoded === false || strlen($decoded) < 3) {
                return null;
            }

            // Validasi magic bytes: harus JPEG (FF D8 FF)
            if (substr($decoded, 0, 3) !== "\xFF\xD8\xFF") {
                return null;
            }

            // Nama file acak agar tidak bisa ditebak
            $token    = bin2hex(random_bytes(8));
            $dir      = "absensi/{$date}";
            $filename = "{$employeeId}_{$type}_{$token}.jpg";
            $path     = "{$dir}/{$filename}";

            // Hapus foto lama jika ada (update selfie hari yang sama)
            $this->deleteOldPhoto($employeeId, $date, $type);

            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($dir);
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, $decoded);

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }

    private function deleteOldPhoto(int $employeeId, string $date, string $type): void
    {
        $field = $type === 'in' ? 'photo_in' : 'photo_out';
        $old   = Attendance::where('employee_id', $employeeId)
                    ->whereDate('date', $date)
                    ->value($field);

        if ($old) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($old);
        }
    }

    // ── Admin: kelola absensi ─────────────────────────────────────────────────

    public function admin(Request $request)
    {
        $date      = $request->filled('tanggal') ? $request->tanggal : today()->toDateString();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::whereDate('date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()->keyBy('employee_id');

        return view('attendances.admin', compact('employees', 'attendances', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'status'      => 'required|in:hadir,izin,sakit,alpha',
            'check_in'    => 'nullable|date_format:H:i',
            'check_out'   => 'nullable|date_format:H:i|after_or_equal:check_in',
            'notes'       => 'nullable|string|max:300',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            [
                'status'    => $request->status,
                'check_in'  => $request->check_in  ? $request->check_in  . ':00' : null,
                'check_out' => $request->check_out ? $request->check_out . ':00' : null,
                'notes'     => $request->notes,
                'input_by'  => auth()->user()->name,
            ]
        );

        return back()->with('success', "Absensi {$employee->name} berhasil disimpan.");
    }

    public function destroy(Attendance $attendance)
    {
        $name = $attendance->employee->name ?? '-';
        $attendance->delete();
        return back()->with('success', "Absensi {$name} berhasil dihapus.");
    }

    // ── Rekap Bulanan ─────────────────────────────────────────────────────────

    public function rekap(Request $request)
    {
        $bulan  = $request->filled('bulan') ? (int) $request->bulan : now()->month;
        $tahun  = $request->filled('tahun') ? (int) $request->tahun : now()->year;

        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::whereMonth('date', $bulan)
            ->whereYear('date', $tahun)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->groupBy('employee_id');

        $rekap = $employees->map(function ($emp) use ($attendances) {
            $data   = $attendances->get($emp->id, collect());
            return [
                'employee' => $emp,
                'hadir'    => $data->where('status', 'hadir')->count(),
                'izin'     => $data->where('status', 'izin')->count(),
                'sakit'    => $data->where('status', 'sakit')->count(),
                'alpha'    => $data->where('status', 'alpha')->count(),
                'total'    => $data->count(),
            ];
        });

        $bulanList = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => \Carbon\Carbon::create()->month($m)->translatedFormat('F')]);
        $tahunList = range(now()->year - 1, now()->year + 1);

        return view('attendances.rekap', compact('rekap', 'bulan', 'tahun', 'bulanList', 'tahunList'));
    }

    public function exportCsv(Request $request)
    {
        $bulan = $request->filled('bulan') ? (int) $request->bulan : now()->month;
        $tahun = $request->filled('tahun') ? (int) $request->tahun : now()->year;

        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $attendances = Attendance::whereMonth('date', $bulan)
            ->whereYear('date', $tahun)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()->groupBy('employee_id');

        $bulanLabel = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
        $filename   = "absensi_{$bulanLabel}_{$tahun}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($employees, $attendances) {
            $f = fopen('php://output', 'w');
            // BOM untuk Excel agar UTF-8 terbaca benar
            fwrite($f, "\xEF\xBB\xBF");
            fputcsv($f, ['No', 'Nama Pegawai', 'Jabatan', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Total Hari']);

            foreach ($employees as $i => $emp) {
                $data = $attendances->get($emp->id, collect());
                fputcsv($f, [
                    $i + 1,
                    $emp->name,
                    $emp->jabatan ?? '-',
                    $data->where('status', 'hadir')->count(),
                    $data->where('status', 'izin')->count(),
                    $data->where('status', 'sakit')->count(),
                    $data->where('status', 'alpha')->count(),
                    $data->count(),
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
