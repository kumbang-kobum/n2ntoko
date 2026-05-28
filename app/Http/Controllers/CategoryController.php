<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:produk.lihat', only: ['index']),
            new Middleware('permission:produk.tambah', only: ['store']),
            new Middleware('permission:produk.edit', only: ['update']),
            new Middleware('permission:produk.hapus', only: ['destroy']),
        ];
    }

    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ], ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Kategori sudah ada.']);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', "Kategori \"{$request->name}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', "unique:categories,name,{$category->id}"],
        ], ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Kategori sudah ada.']);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', "Kategori berhasil diperbarui.");
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', "Kategori \"{$category->name}\" tidak bisa dihapus karena masih memiliki produk.");
        }

        $category->delete();
        return back()->with('success', "Kategori \"{$category->name}\" berhasil dihapus.");
    }
}
