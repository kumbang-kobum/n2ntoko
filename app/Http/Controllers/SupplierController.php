<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pembelian.lihat', only: ['index']),
            new Middleware('permission:pembelian.tambah', only: ['store']),
            new Middleware('permission:pembelian.edit', only: ['update']),
            new Middleware('permission:pembelian.hapus', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:200',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:100',
        ], ['name.required' => 'Nama supplier wajib diisi.']);

        Supplier::create([
            'name'           => $request->name,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'is_active'      => true,
        ]);

        return back()->with('success', "Supplier \"{$request->name}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'           => 'required|string|max:200',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:100',
        ]);

        $supplier->update($request->only(['name','phone','email','address','contact_person']));
        $supplier->update(['is_active' => $request->boolean('is_active', true)]);

        return back()->with('success', "Supplier \"{$supplier->name}\" berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return back()->with('error', "Supplier \"{$supplier->name}\" tidak bisa dihapus karena sudah ada data pembelian.");
        }

        $name = $supplier->name;
        $supplier->delete();

        return back()->with('success', "Supplier \"{$name}\" berhasil dihapus.");
    }

    // API: untuk autocomplete / select2 di form pembelian
    public function apiList(Request $request)
    {
        $suppliers = Supplier::where('is_active', true)
            ->when($request->q, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name','phone']);

        return response()->json($suppliers);
    }
}
