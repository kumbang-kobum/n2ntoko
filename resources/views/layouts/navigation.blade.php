<nav x-data="{ open: false, mobileOpen: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="font-bold text-gray-900 text-base">N2N Toko</span>
                </a>

                <!-- Menu Navigasi -->
                <div class="hidden sm:flex sm:ms-8 sm:gap-1">
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

                    @canany(['penjualan.lihat','penjualan.tambah'])
                    <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                        Penjualan
                    </x-nav-link>
                    @endcanany

                    @can('laporan.lihat')
                    <x-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')">
                        Laporan
                    </x-nav-link>
                    @endcan

                    @can('user.lihat')
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        Pengguna
                    </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                <!-- Badge Role -->
                <span class="text-xs bg-blue-100 text-blue-700 font-medium px-2 py-1 rounded-full capitalize">
                    {{ Auth::user()->getRoleNames()->first() ?? 'user' }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 transition">
                            <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>
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

            <!-- Hamburger Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="mobileOpen = !mobileOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition">
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

    <!-- Mobile Menu -->
    <div :class="{'block': mobileOpen, 'hidden': !mobileOpen}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            @can('produk.lihat')
            <x-responsive-nav-link :href="route('products.index')">Produk & Stok</x-responsive-nav-link>
            @endcan
            @can('produk.edit')
            <x-responsive-nav-link :href="route('stok-opname.index')">Stok Opname</x-responsive-nav-link>
            @endcan
            @can('pembelian.lihat')
            <x-responsive-nav-link :href="route('purchases.index')">Pembelian</x-responsive-nav-link>
            @endcan
            @can('penjualan.lihat')
            <x-responsive-nav-link :href="route('sales.index')">Penjualan</x-responsive-nav-link>
            @endcan
            @can('laporan.lihat')
            <x-responsive-nav-link :href="route('laporan.index')">Laporan</x-responsive-nav-link>
            @endcan
            @can('user.lihat')
            <x-responsive-nav-link :href="route('users.index')">Pengguna</x-responsive-nav-link>
            @endcan
        </div>
        <div class="pt-4 pb-3 border-t border-gray-200 px-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
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
