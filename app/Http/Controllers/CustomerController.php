<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pelanggan.lihat',  only: ['index']),
            new Middleware('permission:pelanggan.tambah', only: ['store']),
            new Middleware('permission:pelanggan.edit',   only: ['update']),
            new Middleware('permission:pelanggan.hapus',  only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $customers = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:150',
            'phone' => 'nullable|string|max:20',
            'type'  => 'required|in:umum,grosir,langganan',
            'address' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'type.required' => 'Tipe pelanggan wajib dipilih.',
        ]);

        Customer::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'type'      => $request->type,
            'address'   => $request->address,
            'is_active' => true,
        ]);

        return back()->with('success', "Pelanggan \"{$request->name}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required|string|max:150',
            'phone'   => 'nullable|string|max:20',
            'type'    => 'required|in:umum,grosir,langganan',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'type'      => $request->type,
            'address'   => $request->address,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Data pelanggan berhasil diperbarui.");
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->where('status', '!=', 'cancelled')->exists()) {
            return back()->with('error', "Pelanggan \"{$customer->name}\" tidak bisa dihapus karena memiliki riwayat transaksi aktif.");
        }

        $customer->delete();
        return back()->with('success', "Pelanggan \"{$customer->name}\" berhasil dihapus.");
    }

    public function apiList(Request $request)
    {
        $customers = Customer::where('is_active', true)
            ->where('name', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'type']);

        return response()->json($customers);
    }
}
