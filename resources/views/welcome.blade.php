<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>N2N Toko — Sistem Grosir</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50" style="font-family: 'Inter', sans-serif;">

    <header class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-gray-900 text-lg leading-none">N2N Toko</div>
                    <div class="text-xs text-gray-500">Sistem Manajemen Grosir</div>
                </div>
            </div>
            @if (Route::has('login'))
                <div class="flex gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            Masuk ke Aplikasi
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="border border-gray-300 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                            Login
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </header>

    <section class="max-w-6xl mx-auto px-4 py-16 text-center">
        <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
            Sistem POS & Manajemen Stok Grosir
        </span>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
            Kelola Grosir Anda<br>
            <span class="text-blue-600">Lebih Mudah & Akurat</span>
        </h1>
        <p class="text-gray-500 text-lg max-w-xl mx-auto mb-8">
            Pencatatan stok multi-satuan, harga grosir & eceran, barcode scanner,
            dan laporan laba rugi otomatis dalam satu aplikasi.
        </p>
        @auth
            <a href="{{ url('/dashboard') }}"
                class="inline-block bg-blue-600 text-white px-8 py-3 rounded-xl font-semibold text-base hover:bg-blue-700 transition shadow-md">
                Buka Dashboard
            </a>
        @else
            <a href="{{ route('login') }}"
                class="inline-block bg-blue-600 text-white px-8 py-3 rounded-xl font-semibold text-base hover:bg-blue-700 transition shadow-md">
                Mulai Sekarang
            </a>
        @endauth
    </section>

    <section class="max-w-6xl mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Barcode Scanner</h3>
                <p class="text-gray-500 text-sm">Scan barcode otomatis kenali produk beserta satuannya. Slop, kotak, karton — semua terkonversi ke stok yang tepat.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Harga Grosir & Eceran</h3>
                <p class="text-gray-500 text-sm">Atur harga berbeda per satuan. Sistem otomatis memilih harga yang sesuai berdasarkan jumlah dan tipe pelanggan.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Laporan Laba Rugi</h3>
                <p class="text-gray-500 text-sm">HPP dihitung otomatis dengan metode average cost. Profit tiap transaksi tercatat akurat meski harga beli berubah.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Multi-Satuan Produk</h3>
                <p class="text-gray-500 text-sm">Satu produk punya banyak satuan dengan konversi otomatis. Beli per slop, jual per kotak — stok selalu akurat.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Hak Akses per Role</h3>
                <p class="text-gray-500 text-sm">Role Admin, Manajer, Kasir, dan Gudang. Setiap staf hanya bisa akses fitur sesuai tugasnya.</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Kartu Stok & Hutang Piutang</h3>
                <p class="text-gray-500 text-sm">Riwayat lengkap setiap pergerakan stok. Pantau hutang ke supplier dan piutang dari pelanggan.</p>
            </div>

        </div>
    </section>

    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-6xl mx-auto px-4 py-6 flex flex-col md:flex-row items-center justify-between gap-2">
            <span class="text-sm text-gray-400">&copy; {{ date('Y') }} N2N Toko. Sistem Manajemen Grosir.</span>
            <span class="text-sm text-gray-400">Laravel {{ app()->version() }}</span>
        </div>
    </footer>

</body>
</html>
