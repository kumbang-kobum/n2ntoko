<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produk & Stok</h2>
            <div class="flex gap-2">
                @can('produk.lihat')
                <a href="{{ route('categories.index') }}"
                    class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Kategori
                </a>
                @endcan
                @can('produk.tambah')
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Produk
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- Filter --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <form method="GET" id="products-filter-form" class="flex flex-wrap gap-3">
                    <div class="flex-1 min-w-[180px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama / SKU / barcode..."
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div class="relative min-w-[180px]">
                        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m0 14v1M4 12H3m18 0h-1M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707"/>
                            </svg>
                        </div>
                        <input type="text" name="barcode" id="barcode-input" value="{{ request('barcode') }}"
                            placeholder="Scan barcode..."
                            autocomplete="off"
                            class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select name="stok" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Stok</option>
                        <option value="aman" {{ request('stok') === 'aman' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="menipis" {{ request('stok') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                        <option value="habis" {{ request('stok') === 'habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                    <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Cari</button>
                    @if(request()->hasAny(['search','category','stok','status','barcode']))
                    <a href="{{ route('products.index') }}" class="border border-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">Reset</a>
                    @endif
                </form>
                <script>
                    document.getElementById('barcode-input').addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            document.getElementById('products-filter-form').submit();
                        }
                    });
                </script>
            </div>

            {{-- Tabel --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Produk</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Kategori</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Stok</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">HPP / Base</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Satuan</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Status</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($products as $product)
                        @php
                            $stockStatus = $product->stock_status;
                            $stockColor = match($stockStatus) {
                                'habis'   => 'text-red-600 bg-red-50',
                                'menipis' => 'text-orange-600 bg-orange-50',
                                default   => 'text-green-600 bg-green-50',
                            };
                            $stockLabel = match($stockStatus) {
                                'habis'   => 'Habis',
                                'menipis' => 'Menipis',
                                default   => 'Aman',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $product->sku }}</div>
                            </td>
                            <td class="px-5 py-4 text-gray-500">
                                {{ $product->category?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-800">
                                    {{ number_format($product->stock_qty, 0, ',', '.') }}
                                    <span class="text-xs text-gray-400 font-normal">{{ $product->baseUnit?->unit_name }}</span>
                                </div>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $stockColor }}">{{ $stockLabel }}</span>
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                @if($product->avg_cost > 0)
                                    Rp {{ number_format($product->avg_cost, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs text-gray-500">{{ $product->units_count }} satuan</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($product->is_active)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Aktif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>Non-aktif
                                </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="text-gray-400 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('produk.edit')
                                    <a href="{{ route('products.edit', $product) }}"
                                        class="text-gray-400 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('produk.hapus')
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                        onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Belum ada produk.
                                @can('produk.tambah')
                                <a href="{{ route('products.create') }}" class="text-blue-600 hover:underline ml-1">Tambah sekarang</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>{{-- /overflow-x-auto --}}
                @if($products->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $products->links() }}</div>
                @endif
            </div>

            @if($products->total())
            <p class="text-xs text-gray-400 text-right">
                Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk
            </p>
            @endif

        </div>
    </div>
</x-app-layout>
