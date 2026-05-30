<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-xl font-semibold text-gray-800">{{ $sale->invoice_number }}</h2>
                @if($sale->status === 'paid')
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">Lunas</span>
                @else
                <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 font-medium">Dibatalkan</span>
                @endif
            </div>
            <div class="flex gap-2">
                @if($sale->status === 'paid')
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900 transition no-print">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Struk
                </button>
                @can('penjualan.batal')
                <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                      onsubmit="return confirm('Batalkan penjualan ini? Stok akan dikembalikan.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50 transition no-print">
                        Batalkan Transaksi
                    </button>
                </form>
                @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">No. Faktur</p>
                        <p class="font-semibold text-gray-800">{{ $sale->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-800">
                            {{ $sale->sale_date->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kasir</p>
                        <p class="font-semibold text-gray-800">{{ $sale->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tipe Harga</p>
                        <p class="font-semibold text-gray-800 capitalize">{{ $sale->price_type }}</p>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-medium text-gray-700">Item Penjualan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Produk</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Satuan</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Qty</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Harga Jual</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $item->unit->unit_name }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    {{ rtrim(rtrim(number_format($item->qty, 4, ',', '.'), '0'), ',') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    Rp {{ number_format($item->sell_price, 0, ',', '.') }}
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
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Bayar</td>
                                <td class="px-4 py-2 text-right text-gray-700">
                                    Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Kembalian</td>
                                <td class="px-4 py-2 text-right font-semibold text-green-700">
                                    Rp {{ number_format($sale->change_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ══ STRUK 80mm THERMAL (hanya muncul saat print) ══ --}}
    <div class="struk-80mm" style="display:none">
        <div style="font-family:'Courier New',monospace; font-size:11px; width:72mm; margin:0 auto; line-height:1.4;">

            {{-- Header --}}
            <div style="text-align:center; margin-bottom:4px;">
                <div style="font-size:14px; font-weight:bold; letter-spacing:1px;">{{ strtoupper(config('app.name')) }}</div>
                <div style="font-size:9px; color:#555;">Sistem POS & Manajemen Grosir</div>
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
            </div>

            {{-- Info Transaksi --}}
            <table style="width:100%; font-size:10px; border-collapse:collapse;">
                <tr><td>No</td><td>:</td><td style="font-weight:bold;">{{ $sale->invoice_number }}</td></tr>
                <tr><td>Tgl</td><td>:</td><td>{{ $sale->sale_date->format('d/m/Y') }} {{ $sale->created_at->format('H:i') }}</td></tr>
                <tr><td>Kasir</td><td>:</td><td>{{ $sale->user?->name }}</td></tr>
                <tr><td>Tipe</td><td>:</td><td>{{ ucfirst($sale->price_type) }}</td></tr>
                <tr><td>Bayar</td><td>:</td><td>{{ $sale->payment_method_label }}</td></tr>
            </table>

            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            {{-- Item --}}
            @foreach($sale->items as $item)
            <div style="font-size:10px;">
                <div style="font-weight:bold;">{{ $item->product->name }}</div>
                <div style="display:flex; justify-content:space-between; padding-left:4px;">
                    <span>{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }} {{ $item->unit->unit_name }} &times; {{ number_format($item->sell_price,0,',','.') }}</span>
                    <span style="font-weight:bold;">{{ number_format($item->subtotal,0,',','.') }}</span>
                </div>
            </div>
            @endforeach

            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            {{-- Total --}}
            <table style="width:100%; font-size:10px; border-collapse:collapse;">
                <tr>
                    <td>Subtotal</td>
                    <td style="text-align:right;">Rp {{ number_format($sale->subtotal_before_tax,0,',','.') }}</td>
                </tr>
                @if($sale->tax_amount > 0)
                <tr>
                    <td>PPN {{ number_format($sale->tax_rate,0) }}%</td>
                    <td style="text-align:right;">Rp {{ number_format($sale->tax_amount,0,',','.') }}</td>
                </tr>
                @endif
                <tr style="font-weight:bold; font-size:12px; border-top:1px solid #000;">
                    <td style="padding-top:3px;">TOTAL</td>
                    <td style="text-align:right; padding-top:3px;">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                </tr>
                @if($sale->payment_method === 'cash')
                <tr>
                    <td>Tunai</td>
                    <td style="text-align:right;">Rp {{ number_format($sale->paid_amount,0,',','.') }}</td>
                </tr>
                <tr>
                    <td>Kembali</td>
                    <td style="text-align:right; font-weight:bold;">Rp {{ number_format($sale->change_amount,0,',','.') }}</td>
                </tr>
                @else
                <tr>
                    <td colspan="2" style="text-align:center; font-size:9px; padding-top:2px;">
                        Lunas via {{ $sale->payment_method_label }}
                    </td>
                </tr>
                @endif
            </table>

            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            {{-- Footer --}}
            <div style="text-align:center; font-size:9px; margin-top:4px;">
                <div>Terima kasih atas kunjungan Anda!</div>
                <div style="margin-top:2px; color:#555;">Barang yang sudah dibeli</div>
                <div style="color:#555;">tidak dapat dikembalikan</div>
                @if($sale->notes)
                <div style="margin-top:4px; font-style:italic;">{{ $sale->notes }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>

@push('styles')
<style>
@media print {
    @page { size: 80mm auto; margin: 3mm; }
    .no-print, nav, header { display: none !important; }
    body > div { display: none !important; }
    .struk-80mm { display: block !important; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
@endpush

@if(request('print'))
@push('scripts')
<script>
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 400);
    });
</script>
@endpush
@endif
