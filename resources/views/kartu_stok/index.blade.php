<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kartu Stok</h2>
                <p class="text-sm text-gray-400 mt-0.5">Pilih produk untuk melihat riwayat pergerakan stok</p>
            </div>
            <a href="{{ route('kartu-stok.cetak-kosong') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700
                      hover:bg-gray-50 px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Form Kosong
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Search --}}
            <form method="GET" action="{{ route('kartu-stok.index') }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama produk atau SKU..."
                           autofocus
                           class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>

            {{-- Daftar produk --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($products->isEmpty())
                <div class="py-16 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    Produk tidak ditemukan
                </div>
                @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Produk</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Satuan</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Stok</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($products as $product)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $product->sku }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">
                                {{ $product->baseUnit?->unit_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @php
                                    $textColor = match($product->stock_status) {
                                        'habis'   => 'text-red-600 font-semibold',
                                        'menipis' => 'text-orange-500 font-semibold',
                                        default   => 'text-gray-800',
                                    };
                                @endphp
                                <span class="{{ $textColor }}">
                                    {{ number_format($product->stock_qty, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $product->category?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('kartu-stok.show', $product) }}"
                                   class="inline-flex items-center gap-1.5 text-xs text-blue-600 font-medium
                                          hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                    Lihat Kartu
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($products->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
                @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
