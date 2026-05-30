<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pengeluaran.lihat',  only: ['index']),
            new Middleware('permission:pengeluaran.tambah', only: ['store']),
            new Middleware('permission:pengeluaran.edit',   only: ['update']),
            new Middleware('permission:pengeluaran.hapus',  only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Expense::with('user');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('dari')) {
            $query->whereDate('expense_date', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('expense_date', '<=', $request->sampai);
        }

        $expenses = $query->latest('expense_date')->latest('id')->paginate(20)->withQueryString();

        $totalBulanIni = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $categories = Expense::categoryLabels();

        return view('expenses.index', compact('expenses', 'totalBulanIni', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'     => 'required|in:sewa,listrik,air,gaji,transport,pemeliharaan,lainnya',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'reference'    => 'nullable|string|max:100',
        ], [
            'category.required'    => 'Kategori wajib dipilih.',
            'description.required' => 'Keterangan wajib diisi.',
            'amount.required'      => 'Jumlah wajib diisi.',
            'expense_date.required'=> 'Tanggal wajib diisi.',
        ]);

        Expense::create([
            'user_id'      => auth()->id(),
            'category'     => $request->category,
            'description'  => $request->description,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'reference'    => $request->reference,
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category'     => 'required|in:sewa,listrik,air,gaji,transport,pemeliharaan,lainnya',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'reference'    => 'nullable|string|max:100',
        ]);

        $expense->update([
            'category'     => $request->category,
            'description'  => $request->description,
            'amount'       => $request->amount,
            'expense_date' => $request->expense_date,
            'reference'    => $request->reference,
        ]);

        return back()->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
