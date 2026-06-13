<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
                    <p class="text-sm text-gray-400 font-mono">{{ $product->sku }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kartu-stok.show', $product) }}"
                   class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700
                          hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Kartu Stok
                </a>
                @can('produk.edit')
                <a href="{{ route('products.edit', $product) }}"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Produk
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Info Umum --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $statusColor = match($product->stock_status) {
                        'habis'   => 'bg-red-50 border-red-100',
                        'menipis' => 'bg-orange-50 border-orange-100',
                        default   => 'bg-green-50 border-green-100',
                    };
                    $stockTextColor = match($product->stock_status) {
                        'habis'   => 'text-red-600',
                        'menipis' => 'text-orange-600',
                        default   => 'text-green-600',
                    };
                @endphp
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="text-xs text-gray-400 mb-1">Stok Saat Ini</div>
                    <div class="text-2xl font-bold {{ $stockTextColor }}">
                        {{ number_format($product->stock_qty, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400">{{ $product->baseUnit?->unit_name }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="text-xs text-gray-400 mb-1">HPP Rata-rata</div>
                    <div class="text-lg font-bold text-gray-800">
                        {{ $product->avg_cost > 0 ? 'Rp '.number_format($product->avg_cost,0,',','.') : '—' }}
                    </div>
                    <div class="text-xs text-gray-400">per {{ $product->baseUnit?->unit_name }}</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="text-xs text-gray-400 mb-1">Nilai Stok</div>
                    <div class="text-lg font-bold text-gray-800">
                        Rp {{ number_format($product->stock_qty * $product->avg_cost, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-400">stok × HPP</div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="text-xs text-gray-400 mb-1">Kategori</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $product->category?->name ?? '—' }}</div>
                    <div class="text-xs text-gray-400 mt-1">
                        @if($product->is_active)
                        <span class="text-green-600">● Aktif</span>
                        @else
                        <span class="text-gray-400">● Non-aktif</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabel Satuan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-700 text-sm">Satuan, Barcode & Harga</h3>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Satuan</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Konversi</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Barcode</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Harga Eceran</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Harga Grosir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($product->units as $unit)
                        <tr class="{{ $unit->is_base ? 'bg-blue-50/40' : '' }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-800">{{ $unit->unit_name }}</span>
                                    @if($unit->is_base)
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">Dasar</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                @if($unit->is_base)
                                    <span class="text-xs bg-blue-50 text-blue-500 px-2 py-0.5 rounded">
                                        Satuan terkecil
                                    </span>
                                @else
                                    <span class="font-medium">
                                        1 {{ $unit->unit_name }}
                                        = {{ number_format($unit->conversion, 0, ',', '.') }}
                                        {{ $product->baseUnit?->unit_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($unit->barcodes->first())
                                <span class="font-mono text-sm bg-gray-100 px-2 py-0.5 rounded text-gray-700">
                                    {{ $unit->barcodes->first()->barcode }}
                                </span>
                                @else
                                <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-700">
                                @php $eceran = $unit->prices->where('price_type','eceran')->first(); @endphp
                                {{ $eceran ? 'Rp '.number_format($eceran->price,0,',','.') : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-700">
                                @php $grosir = $unit->prices->where('price_type','grosir')->first(); @endphp
                                @if($grosir)
                                    Rp {{ number_format($grosir->price,0,',','.') }}
                                    <span class="text-xs text-gray-400">(min {{ number_format($grosir->min_qty,0,',','.') }})</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Deskripsi --}}
            @if($product->description)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-700 text-sm mb-2">Deskripsi</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $product->description }}</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
