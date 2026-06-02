<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pegawai.lihat',  only: ['index']),
            new Middleware('permission:pegawai.tambah', only: ['store']),
            new Middleware('permission:pegawai.edit',   only: ['update']),
            new Middleware('permission:pegawai.hapus',  only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('jabatan', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $employees = $query->orderBy('name')->paginate(20)->withQueryString();

        // Ambil user yang belum punya pegawai untuk pilihan link akun
        $availableUsers = User::whereDoesntHave('employee')
            ->orderBy('name')->get(['id', 'name', 'email']);

        return view('employees.index', compact('employees', 'availableUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:150',
            'jabatan'   => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:300',
            'join_date' => 'nullable|date',
            'user_id'   => 'nullable|exists:users,id|unique:employees,user_id',
        ], [
            'user_id.unique' => 'Akun pengguna ini sudah terhubung ke pegawai lain.',
        ]);

        Employee::create([
            'name'      => $request->name,
            'jabatan'   => $request->jabatan,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'join_date' => $request->join_date,
            'user_id'   => $request->user_id ?: null,
            'is_active' => true,
        ]);

        return back()->with('success', "Pegawai \"{$request->name}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name'      => 'required|string|max:150',
            'jabatan'   => 'nullable|string|max:100',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:300',
            'join_date' => 'nullable|date',
            'user_id'   => "nullable|exists:users,id|unique:employees,user_id,{$employee->id}",
        ]);

        $employee->update([
            'name'      => $request->name,
            'jabatan'   => $request->jabatan,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'join_date' => $request->join_date,
            'user_id'   => $request->user_id ?: null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $name = $employee->name;
        $employee->delete();
        return back()->with('success', "Pegawai \"{$name}\" berhasil dihapus.");
    }
}
