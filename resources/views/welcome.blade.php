<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no,date=no,address=no,email=no">
    <title>N2N Toko — Sistem Grosir</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50" role="application" style="font-family: 'Inter', sans-serif;">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="font-bold text-gray-900 text-base">N2N Toko</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('login') }}"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-12">

        {{-- Hero --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Sistem POS & Manajemen Stok Grosir
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-5 leading-tight">
                Kelola Grosir Anda<br>
                <span class="text-blue-600">Lebih Mudah & Akurat</span>
            </h1>
            <div class="flex flex-wrap justify-center gap-3 text-sm text-gray-500 mb-8">
                <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-4 py-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Stok Multi-Satuan
                </span>
                <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-4 py-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Barcode Scanner
                </span>
                <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-4 py-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Laporan Laba Rugi
                </span>
                <span class="flex items-center gap-1.5 bg-white border border-gray-200 rounded-full px-4 py-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Harga Grosir & Eceran
                </span>
            </div>
            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold text-base hover:bg-blue-700 transition shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                </svg>
                Masuk ke Aplikasi
            </a>
        </div>

        {{-- Feature Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">Barcode Scanner</div>
                    <div class="text-xs text-gray-500 leading-relaxed">Scan otomatis — slop, kotak, karton terkonversi ke stok tepat</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">Harga Grosir & Eceran</div>
                    <div class="text-xs text-gray-500 leading-relaxed">Harga berbeda per satuan, dipilih otomatis sesuai tipe pelanggan</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">Laporan Laba Rugi</div>
                    <div class="text-xs text-gray-500 leading-relaxed">HPP average cost otomatis, profit tiap transaksi tercatat akurat</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">Multi-Satuan Produk</div>
                    <div class="text-xs text-gray-500 leading-relaxed">Beli per slop, jual per kotak — stok selalu akurat dengan konversi otomatis</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">Hak Akses per Role</div>
                    <div class="text-xs text-gray-500 leading-relaxed">Admin, Manajer, Kasir, Gudang — akses sesuai tugas</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-start gap-4">
                <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-sm mb-1">Kartu Stok & Hutang</div>
                    <div class="text-xs text-gray-500 leading-relaxed">Riwayat lengkap pergerakan stok dan hutang ke supplier</div>
                </div>
            </div>

        </div>
    </div>

    <footer class="border-t border-gray-200 bg-white mt-8">
        <div class="max-w-6xl mx-auto px-4 py-5 flex items-center justify-between">
            <span class="text-xs text-gray-400">&copy; {{ date('Y') }} N2N Toko</span>
            <a href="{{ route('login') }}" class="text-xs text-blue-600 font-medium hover:underline">Login →</a>
        </div>
    </footer>

</body>
</html>
