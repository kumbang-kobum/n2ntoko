<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.index') }}"
                   class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-xl font-semibold text-gray-800">{{ $purchase->invoice_number }}</h2>
                @php
                    $badgeColors = ['draft'=>'yellow','ordered'=>'blue','confirmed'=>'green','paid'=>'gray'];
                    $badgeLabels = ['draft'=>'Draft','ordered'=>'Dipesan','confirmed'=>'Diterima','paid'=>'Lunas'];
                    $bc = $badgeColors[$purchase->status] ?? 'gray';
                    $bl = $badgeLabels[$purchase->status] ?? $purchase->status;
                @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $bc }}-100 text-{{ $bc }}-700">
                    {{ $bl }}
                </span>
            </div>
            @if(in_array($purchase->status, ['draft', 'ordered']))
            <div class="flex gap-2">
                @if($purchase->status === 'draft')
                    @can('pembelian.edit')
                    <a href="{{ route('purchases.edit', $purchase) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                        Edit
                    </a>
                    @endcan
                @endif
                @can('pembelian.konfirmasi')
                <form method="POST" action="{{ route('purchases.confirm', $purchase) }}"
                      onsubmit="return confirm('Konfirmasi pembelian ini? Stok akan diperbarui.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                        Konfirmasi & Update Stok
                    </button>
                </form>
                @endcan
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">No. Faktur</p>
                        <p class="font-semibold text-gray-800">{{ $purchase->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-800">
                            {{ $purchase->purchase_date->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Supplier</p>
                        <p class="font-semibold text-gray-800">{{ $purchase->supplier?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dibuat oleh</p>
                        <p class="font-semibold text-gray-800">{{ $purchase->user?->name ?? '-' }}</p>
                    </div>
                    @if($purchase->notes)
                    <div class="col-span-2 sm:col-span-4">
                        <p class="text-xs text-gray-500">Catatan</p>
                        <p class="text-gray-700">{{ $purchase->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-medium text-gray-700">Item Pembelian</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Produk</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Satuan</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Qty</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Harga Beli</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($purchase->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $item->unit->unit_name }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ number_format($item->qty, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-800">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900 text-base">
                                    Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Pembayaran Hutang --}}
            @if($purchase->status === 'confirmed' && $purchase->hutang > 0)
            @can('pembelian.edit')
            <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-5" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-orange-700">Sisa Hutang ke Supplier</p>
                        <p class="text-2xl font-bold text-orange-600 mt-0.5">
                            Rp {{ number_format($purchase->hutang, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Sudah dibayar: Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}
                            dari Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Catat Pembayaran
                    </button>
                </div>

                <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-orange-100">
                    <form method="POST" action="{{ route('purchases.pay', $purchase) }}" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Jumlah Bayar (maks Rp {{ number_format($purchase->hutang, 0, ',', '.') }})
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                                <input type="number" name="bayar"
                                       value="{{ old('bayar', $purchase->hutang) }}"
                                       min="1" max="{{ $purchase->hutang }}" step="500"
                                       class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                                              focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            @error('bayar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit"
                                onclick="return confirm('Konfirmasi pembayaran ini?')"
                                class="px-5 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition">
                            Simpan
                        </button>
                        <button type="button" @click="open = false"
                                class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                    </form>
                </div>
            </div>
            @endcan
            @endif

            @if($purchase->status === 'paid')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-green-800">Pembelian ini sudah LUNAS</p>
                    <p class="text-xs text-green-600">Total dibayar: Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
