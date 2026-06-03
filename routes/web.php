<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

// Absensi publik (tanpa login) — dibatasi 15 request/menit per IP
Route::get('/absensi',        [AttendanceController::class, 'publicPage'])->name('absensi.public');
Route::middleware('throttle:15,1')->group(function () {
    Route::post('/absensi/masuk', [AttendanceController::class, 'checkIn'])->name('absensi.masuk');
    Route::post('/absensi/pulang',[AttendanceController::class, 'checkOut'])->name('absensi.pulang');
});

Route::get('/dashboard', function () {
    $omzetHariIni = Sale::whereDate('sale_date', today())
        ->where('status', 'paid')->sum('total_amount');

    $labaHariIni = SaleItem::whereHas('sale', fn($q) =>
        $q->whereDate('sale_date', today())->where('status', 'paid'))
        ->sum('profit');

    $transaksiHariIni = Sale::whereDate('sale_date', today())
        ->where('status', 'paid')->count();

    $totalProduk = Product::where('is_active', true)->count();

    $stokMenipis = Product::where('is_active', true)
        ->where('stock_qty', '>', 0)
        ->whereColumn('stock_qty', '<=', 'min_stock')
        ->count();

    $tokoNama = \App\Models\Setting::get('toko_nama', config('app.name'));

    return view('dashboard', compact(
        'omzetHariIni', 'labaHariIni', 'transaksiHariIni',
        'totalProduk', 'stokMenipis', 'tokoNama'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users',     UserController::class)->except('show');
    Route::resource('employees', EmployeeController::class)->except(['create','edit','show']);

    // Absensi admin
    Route::get('absensi/rekap/export', [AttendanceController::class, 'exportCsv'])->name('absensi.export');
    Route::get('absensi/rekap',        [AttendanceController::class, 'rekap'])->name('absensi.rekap');
    Route::get('absensi/harian',       [AttendanceController::class, 'admin'])->name('absensi.admin');
    Route::post('absensi/harian',      [AttendanceController::class, 'store'])->name('absensi.store');
    Route::delete('absensi/{attendance}', [AttendanceController::class, 'destroy'])->name('absensi.destroy');

    Route::get('hak-akses',                [HakAksesController::class, 'index'])->name('hakakses.index');
    Route::post('hak-akses/roles',         [HakAksesController::class, 'storeRole'])->name('hakakses.storeRole');
    Route::delete('hak-akses/roles/{role}',[HakAksesController::class, 'destroyRole'])->name('hakakses.destroyRole');
    Route::put('hak-akses/{role}',         [HakAksesController::class, 'update'])->name('hakakses.update');

    Route::get('pengaturan',  [SettingController::class, 'index'])->name('settings.index');
    Route::put('pengaturan',  [SettingController::class, 'update'])->name('settings.update');
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('products', ProductController::class);

    Route::resource('suppliers', SupplierController::class)->except(['create','edit','show']);

    Route::resource('purchases', PurchaseController::class);
    Route::post('purchases/{purchase}/order',   [PurchaseController::class, 'order'])->name('purchases.order');
    Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])->name('purchases.confirm');
    Route::post('purchases/{purchase}/pay',     [PurchaseController::class, 'pay'])->name('purchases.pay');

    // API endpoints for purchase form
    Route::middleware('throttle:120,1')->group(function () {
        Route::get('api/products/search',        [PurchaseController::class, 'apiSearch'])->name('api.products.search');
        Route::get('api/barcodes/{barcode}',     [PurchaseController::class, 'apiBarcode'])->name('api.barcodes');
        Route::get('api/sales/search',           [SaleController::class,    'apiSearch'])->name('api.sales.search');
        Route::get('api/sales/barcode/{barcode}',[SaleController::class,    'apiBarcode'])->name('api.sales.barcode');
        Route::get('api/suppliers',              [SupplierController::class, 'apiList'])->name('api.suppliers');
        Route::get('api/customers',              [CustomerController::class, 'apiList'])->name('api.customers');
    });

    Route::resource('sales', SaleController::class)->only(['index','create','store','show','destroy']);

    Route::resource('customers', CustomerController::class)->except(['create','edit','show']);

    Route::resource('expenses', ExpenseController::class)->except(['create','edit','show']);

    Route::get('laporan',       [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/shift', [LaporanController::class, 'shift'])->name('laporan.shift');

    Route::get('stok-opname',  [StokOpnameController::class, 'index'])->name('stok-opname.index');
    Route::post('stok-opname', [StokOpnameController::class, 'store'])->name('stok-opname.store');
});

require __DIR__.'/auth.php';
