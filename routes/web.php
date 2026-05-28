<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('products', ProductController::class);

    Route::resource('suppliers', SupplierController::class)->except(['create','edit','show']);
    Route::get('api/suppliers', [SupplierController::class, 'apiList'])->name('api.suppliers');

    Route::resource('purchases', PurchaseController::class);
    Route::post('purchases/{purchase}/order',   [PurchaseController::class, 'order'])->name('purchases.order');
    Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])->name('purchases.confirm');

    // API endpoints for purchase form
    Route::get('api/products/search', [PurchaseController::class, 'apiSearch'])->name('api.products.search');
    Route::get('api/barcodes/{barcode}', [PurchaseController::class, 'apiBarcode'])->name('api.barcodes');

    Route::resource('sales', SaleController::class)->only(['index','create','store','show','destroy']);
    Route::get('api/sales/search',          [SaleController::class, 'apiSearch'])->name('api.sales.search');
    Route::get('api/sales/barcode/{barcode}',[SaleController::class, 'apiBarcode'])->name('api.sales.barcode');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::get('stok-opname',  [StokOpnameController::class, 'index'])->name('stok-opname.index');
    Route::post('stok-opname', [StokOpnameController::class, 'store'])->name('stok-opname.store');
});

require __DIR__.'/auth.php';
