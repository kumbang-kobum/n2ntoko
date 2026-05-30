<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Selamat Datang -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl p-6 text-white">
                <p class="text-blue-100 text-sm mb-1">Selamat datang kembali,</p>
                <h3 class="text-2xl font-bold">{{ Auth::user()->name }}</h3>
                <p class="text-blue-100 text-sm mt-1 capitalize">
                    Role: {{ Auth::user()->getRoleNames()->first() ?? '-' }}
                    &nbsp;&middot;&nbsp; {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <!-- Ringkasan Cepat -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-500">Penjualan Hari Ini</span>
                        <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($omzetHariIni, 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $transaksiHariIni }} transaksi</div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-500">Laba Hari Ini</span>
                        <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($labaHariIni, 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">laba kotor</div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-500">Total Produk</span>
                        <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalProduk }}</div>
                    <div class="text-xs text-gray-400 mt-1">jenis produk</div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-500">Stok Menipis</span>
                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stokMenipis }}</div>
                    <div class="text-xs text-gray-400 mt-1">produk perlu restok</div>
                </div>
            </div>

            <!-- Akses Cepat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-semibold text-gray-700 mb-4">Akses Cepat</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @can('penjualan.tambah')
                    <a href="{{ route('sales.create') }}"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl bg-green-50 hover:bg-green-100 transition text-center">
                        <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-green-800">Transaksi Baru</span>
                    </a>
                    @endcan

                    @can('pembelian.tambah')
                    <a href="{{ route('purchases.create') }}"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition text-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-blue-800">Input Pembelian</span>
                    </a>
                    @endcan

                    @can('produk.tambah')
                    <a href="{{ route('products.create') }}"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl bg-orange-50 hover:bg-orange-100 transition text-center">
                        <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-orange-800">Tambah Produk</span>
                    </a>
                    @endcan

                    @can('laporan.lihat')
                    <a href="{{ route('laporan.index') }}"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition text-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-purple-800">Lihat Laporan</span>
                    </a>
                    @endcan
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
