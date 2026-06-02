<nav x-data="{ mobileOpen: false }" class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex justify-between h-14 md:h-16">

            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="font-bold text-gray-900 text-base">{{ \App\Models\Setting::get('toko_nama', 'N2N Toko') }}</span>
                </a>

                <!-- Menu Navigasi Desktop (md+) -->
                <div class="hidden md:flex md:ms-6 md:gap-0.5">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    @canany(['produk.lihat', 'pembelian.lihat'])
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md
                                {{ request()->routeIs('products.*', 'categories.*', 'purchases.*', 'suppliers.*', 'stok-opname.*') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}
                                transition">
                                Inventaris
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @can('produk.lihat')
                            <x-dropdown-link :href="route('products.index')">Produk & Stok</x-dropdown-link>
                            <x-dropdown-link :href="route('categories.index')">Kategori</x-dropdown-link>
                            @endcan
                            @can('produk.edit')
                            <x-dropdown-link :href="route('stok-opname.index')">Stok Opname</x-dropdown-link>
                            @endcan
                            @can('pembelian.lihat')
                            <x-dropdown-link :href="route('suppliers.index')">Supplier</x-dropdown-link>
                            <x-dropdown-link :href="route('purchases.index')">Pembelian</x-dropdown-link>
                            @endcan
                        </x-slot>
                    </x-dropdown>
                    @endcanany

                    @canany(['penjualan.lihat','penjualan.tambah','pelanggan.lihat'])
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md
                                {{ request()->routeIs('sales.*', 'customers.*') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}
                                transition">
                                Penjualan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @canany(['penjualan.lihat','penjualan.tambah'])
                            <x-dropdown-link :href="route('sales.index')">Transaksi Penjualan</x-dropdown-link>
                            @endcanany
                            @can('pelanggan.lihat')
                            <x-dropdown-link :href="route('customers.index')">Pelanggan</x-dropdown-link>
                            @endcan
                        </x-slot>
                    </x-dropdown>
                    @endcanany

                    @can('pengeluaran.lihat')
                    <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                        Pengeluaran
                    </x-nav-link>
                    @endcan

                    @can('laporan.lihat')
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md
                                {{ request()->routeIs('laporan.*') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}
                                transition">
                                Laporan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('laporan.index')">Laporan Keuangan</x-dropdown-link>
                            <x-dropdown-link :href="route('laporan.shift')">Laporan Per Kasir</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endcan

                    @canany(['absensi.lihat','absensi.rekap'])
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md
                                {{ request()->routeIs('absensi.*') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}
                                transition">
                                Absensi
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @can('absensi.lihat')
                            <x-dropdown-link :href="route('absensi.admin')">Absensi Harian</x-dropdown-link>
                            @endcan
                            @can('absensi.rekap')
                            <x-dropdown-link :href="route('absensi.rekap')">Rekap Bulanan</x-dropdown-link>
                            @endcan
                        </x-slot>
                    </x-dropdown>
                    @endcanany

                    @canany(['user.lihat','pegawai.lihat','hakakses.lihat','pengaturan.lihat'])
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-md
                                {{ request()->routeIs('users.*','employees.*','hakakses.*','settings.*') ? 'text-blue-600' : 'text-gray-600 hover:text-gray-900' }}
                                transition">
                                Admin
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @can('user.lihat')
                            <x-dropdown-link :href="route('users.index')">Pengguna</x-dropdown-link>
                            @endcan
                            @can('pegawai.lihat')
                            <x-dropdown-link :href="route('employees.index')">Pegawai</x-dropdown-link>
                            @endcan
                            @can('hakakses.lihat')
                            <x-dropdown-link :href="route('hakakses.index')">Hak Akses</x-dropdown-link>
                            @endcan
                            @can('pengaturan.lihat')
                            <x-dropdown-link :href="route('settings.index')">Pengaturan Toko</x-dropdown-link>
                            @endcan
                        </x-slot>
                    </x-dropdown>
                    @endcanany
                </div>
            </div>

            <!-- User Dropdown Desktop (md+) -->
            <div class="hidden md:flex md:items-center md:gap-3">
                <span class="text-xs bg-blue-100 text-blue-700 font-medium px-2.5 py-1 rounded-full capitalize">
                    {{ Auth::user()->getRoleNames()->first() ?? 'user' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 transition">
                            <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil Saya</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (< md) -->
            <div class="flex items-center md:hidden">
                <button @click="mobileOpen = !mobileOpen"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile/Tablet Menu (< md) -->
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="md:hidden border-t border-gray-100 bg-white shadow-lg"
         style="display:none">
        <div class="px-4 py-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>

            @can('produk.lihat')
            <x-responsive-nav-link :href="route('products.index')">Produk & Stok</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')">Kategori</x-responsive-nav-link>
            @endcan
            @can('produk.edit')
            <x-responsive-nav-link :href="route('stok-opname.index')">Stok Opname</x-responsive-nav-link>
            @endcan
            @can('pembelian.lihat')
            <x-responsive-nav-link :href="route('suppliers.index')">Supplier</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('purchases.index')">Pembelian</x-responsive-nav-link>
            @endcan
            @canany(['penjualan.lihat','penjualan.tambah'])
            <x-responsive-nav-link :href="route('sales.index')">Transaksi Penjualan</x-responsive-nav-link>
            @endcanany
            @can('pelanggan.lihat')
            <x-responsive-nav-link :href="route('customers.index')">Pelanggan</x-responsive-nav-link>
            @endcan
            @can('pengeluaran.lihat')
            <x-responsive-nav-link :href="route('expenses.index')">Pengeluaran</x-responsive-nav-link>
            @endcan
            @can('laporan.lihat')
            <x-responsive-nav-link :href="route('laporan.index')">Laporan Keuangan</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('laporan.shift')">Laporan Per Kasir</x-responsive-nav-link>
            @endcan
            @can('user.lihat')
            <x-responsive-nav-link :href="route('users.index')">Pengguna</x-responsive-nav-link>
            @endcan
            @can('pegawai.lihat')
            <x-responsive-nav-link :href="route('employees.index')">Pegawai</x-responsive-nav-link>
            @endcan
            @can('hakakses.lihat')
            <x-responsive-nav-link :href="route('hakakses.index')">Hak Akses</x-responsive-nav-link>
            @endcan
            @can('pengaturan.lihat')
            <x-responsive-nav-link :href="route('settings.index')">Pengaturan Toko</x-responsive-nav-link>
            @endcan
        </div>

        <div class="border-t border-gray-100 px-4 py-3">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-gray-800 text-sm">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'user' }}</div>
                </div>
            </div>
            <x-responsive-nav-link :href="route('profile.edit')">Profil Saya</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    Keluar
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
