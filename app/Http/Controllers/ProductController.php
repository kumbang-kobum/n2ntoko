<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:produk.lihat', only: ['index', 'show']),
            new Middleware('permission:produk.tambah', only: ['create', 'store']),
            new Middleware('permission:produk.edit', only: ['edit', 'update']),
            new Middleware('permission:produk.hapus', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'baseUnit'])
            ->withCount('units');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        if ($request->filled('stok')) {
            match ($request->stok) {
                'habis'   => $query->where('stock_qty', '<=', 0),
                'menipis' => $query->whereColumn('stock_qty', '<=', 'min_stock')->where('stock_qty', '>', 0),
                'aman'    => $query->whereColumn('stock_qty', '>', 'min_stock'),
                default   => null,
            };
        }

        $products   = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $sku        = Product::generateSku();
        return view('products.create', compact('categories', 'sku'));
    }

    public function store(Request $request)
    {
        $this->validateProduct($request);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'name'        => $request->name,
                'sku'         => $request->sku ?: Product::generateSku(),
                'category_id' => $request->category_id ?: null,
                'description' => $request->description,
                'min_stock'   => $request->min_stock ?? 0,
                'is_active'   => $request->boolean('is_active', true),
                'stock_qty'   => 0,
                'avg_cost'    => 0,
            ]);

            $this->syncUnits($product, $request->units, (int) $request->base_unit_index);
        });

        return redirect()->route('products.index')
            ->with('success', "Produk \"{$request->name}\" berhasil ditambahkan.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'baseUnit', 'units.barcodes', 'units.prices']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['units.barcodes', 'units.prices']);
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->validateProduct($request, $product->id);

        DB::transaction(function () use ($request, $product) {
            $product->update([
                'name'        => $request->name,
                'sku'         => $request->sku ?: $product->sku,
                'category_id' => $request->category_id ?: null,
                'description' => $request->description,
                'min_stock'   => $request->min_stock ?? 0,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            $this->syncUnits($product, $request->units, (int) $request->base_unit_index, update: true);
        });

        return redirect()->route('products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        // Cek apakah ada transaksi yang mereferensikan produk ini
        $hasTransactions = DB::table('purchase_items')->where('product_id', $product->id)->exists()
            || DB::table('sale_items')->where('product_id', $product->id)->exists();

        if ($hasTransactions) {
            return back()->with('error', "Produk \"{$product->name}\" tidak bisa dihapus karena sudah ada transaksi.");
        }

        $name = $product->name;
        // Hapus relasi dulu
        $product->barcodes()->delete();
        $product->prices()->delete();
        $product->units()->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Produk \"{$name}\" berhasil dihapus.");
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function validateProduct(Request $request, ?int $productId = null): void
    {
        $skuRule = $productId
            ? "nullable|string|max:50|unique:products,sku,{$productId}"
            : 'nullable|string|max:50|unique:products,sku';

        $request->validate([
            'name'                          => 'required|string|max:200',
            'sku'                           => $skuRule,
            'category_id'                   => 'nullable|exists:categories,id',
            'min_stock'                     => 'nullable|numeric|min:0',
            'base_unit_index'               => 'required|integer|min:0',
            'units'                         => 'required|array|min:1',
            'units.*.unit_name'             => 'required|string|max:50',
            'units.*.conversion'            => 'required|numeric|min:0.0001',
            'units.*.barcode'               => 'nullable|string|max:100',
            'units.*.price_eceran'          => 'nullable|numeric|min:0',
            'units.*.price_grosir'          => 'nullable|numeric|min:0',
            'units.*.min_qty_grosir'        => 'nullable|numeric|min:1',
        ], [
            'name.required'                 => 'Nama produk wajib diisi.',
            'units.required'                => 'Minimal satu satuan harus ditambahkan.',
            'units.*.unit_name.required'    => 'Nama satuan wajib diisi.',
            'units.*.conversion.required'   => 'Nilai konversi wajib diisi.',
            'units.*.conversion.min'        => 'Nilai konversi harus lebih dari 0.',
        ]);

        // Validasi barcode unik (kecuali milik produk ini sendiri saat update)
        $barcodes = collect($request->units)->pluck('barcode')->filter()->values();
        if ($barcodes->count() !== $barcodes->unique()->count()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'units' => 'Barcode tidak boleh duplikat dalam satu produk.',
            ]);
        }

        foreach ($barcodes as $barcode) {
            $exists = ProductBarcode::where('barcode', $barcode)
                ->when($productId, fn($q) => $q->where('product_id', '!=', $productId))
                ->exists();
            if ($exists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'units' => "Barcode \"{$barcode}\" sudah digunakan produk lain.",
                ]);
            }
        }
    }

    private function syncUnits(Product $product, array $units, int $baseIndex, bool $update = false): void
    {
        if ($update) {
            // Hapus semua unit lama yang tidak direferensikan transaksi
            $product->units->each(function ($unit) {
                $inUse = DB::table('purchase_items')->where('unit_id', $unit->id)->exists()
                    || DB::table('sale_items')->where('unit_id', $unit->id)->exists();
                if (!$inUse) {
                    $unit->barcodes()->delete();
                    $unit->prices()->delete();
                    $unit->delete();
                }
            });
        }

        $baseUnitId = null;

        foreach ($units as $i => $data) {
            $isBase = ($i === $baseIndex);

            $unit = $product->units()->create([
                'unit_name'  => $data['unit_name'],
                'conversion' => $data['conversion'],
                'is_base'    => $isBase,
            ]);

            if ($isBase) {
                $baseUnitId = $unit->id;
            }

            if (!empty($data['barcode'])) {
                $product->barcodes()->create([
                    'unit_id' => $unit->id,
                    'barcode' => trim($data['barcode']),
                ]);
            }

            if (!empty($data['price_eceran'])) {
                $product->prices()->create([
                    'unit_id'    => $unit->id,
                    'price_type' => 'eceran',
                    'price'      => $data['price_eceran'],
                    'min_qty'    => 1,
                ]);
            }

            if (!empty($data['price_grosir'])) {
                $product->prices()->create([
                    'unit_id'    => $unit->id,
                    'price_type' => 'grosir',
                    'price'      => $data['price_grosir'],
                    'min_qty'    => $data['min_qty_grosir'] ?? 1,
                ]);
            }
        }

        $product->update(['base_unit_id' => $baseUnitId]);
    }
}
