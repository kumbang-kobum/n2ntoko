<x-app-layout>
    @php $cfg = \App\Models\Setting::all(); @endphp
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
            @if($sale->status === 'paid')
            <div class="flex gap-2 no-print">
                <button onclick="cetakStruk()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Struk 80mm
                </button>
                <button onclick="cetakA4()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Cetak A4
                </button>
                @can('penjualan.batal')
                <form method="POST" action="{{ route('sales.destroy', $sale) }}"
                      onsubmit="return confirm('Batalkan penjualan ini? Stok akan dikembalikan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50 transition">
                        Batalkan Transaksi
                    </button>
                </form>
                @endcan
            </div>
            @endif
        </div>
    </x-slot>

    {{-- ═══ TAMPILAN LAYAR ═══ --}}
    <div id="screen-content" class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm no-print">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">No. Faktur</p>
                        <p class="font-semibold text-gray-800">{{ $sale->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal</p>
                        <p class="font-semibold text-gray-800">{{ $sale->sale_date->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kasir</p>
                        <p class="font-semibold text-gray-800">{{ $sale->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Metode Bayar</p>
                        <p class="font-semibold text-gray-800">{{ $sale->payment_method_label }}</p>
                    </div>
                    @if($sale->buyer_name)
                    <div>
                        <p class="text-xs text-gray-500">Nama Pembeli</p>
                        <p class="font-semibold text-gray-800">{{ $sale->buyer_name }}</p>
                    </div>
                    @endif
                    @if($sale->tax_amount > 0)
                    <div>
                        <p class="text-xs text-gray-500">PPN {{ number_format($sale->tax_rate, 0) }}%</p>
                        <p class="font-semibold text-gray-800">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>

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
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Harga</th>
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
                            @if($sale->tax_amount > 0)
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Subtotal</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($sale->subtotal_before_tax, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">PPN {{ number_format($sale->tax_rate,0) }}%</td>
                                <td class="px-4 py-2 text-right text-yellow-600">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900 text-base">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if($sale->payment_method === 'cash')
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Bayar (Tunai)</td>
                                <td class="px-4 py-2 text-right">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Kembalian</td>
                                <td class="px-4 py-2 text-right font-semibold text-green-700">
                                    Rp {{ number_format($sale->change_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @else
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Lunas via</td>
                                <td class="px-4 py-2 text-right font-semibold text-blue-700">{{ $sale->payment_method_label }}</td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══ STRUK 80mm ═══ --}}
    <div id="print-struk" style="display:none">
        <div style="font-family:'Courier New',monospace; font-size:11px; width:72mm; margin:0 auto; line-height:1.45; color:#000;">

            <div style="text-align:center; margin-bottom:5px;">
                <div style="font-size:14px; font-weight:bold; letter-spacing:1px;">{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</div>
                @if(!empty($cfg['toko_tagline']))
                <div style="font-size:9px;">{{ $cfg['toko_tagline'] }}</div>
                @endif
                @if(!empty($cfg['nota_header']))
                <div style="font-size:9px;">{{ $cfg['nota_header'] }}</div>
                @endif
                @if(!empty($cfg['toko_alamat']))
                <div style="font-size:9px;">{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</div>
                @endif
                @if(!empty($cfg['toko_telepon']))
                <div style="font-size:9px;">Telp: {{ $cfg['toko_telepon'] }}</div>
                @endif
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
                <div style="font-size:10px; font-weight:bold;">NOTA PENJUALAN</div>
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
            </div>

            <table style="width:100%; font-size:10px; border-collapse:collapse; margin-bottom:4px;">
                <tr><td style="width:36%">No</td><td style="width:4%">:</td><td><b>{{ $sale->invoice_number }}</b></td></tr>
                <tr><td>Tanggal</td><td>:</td><td>{{ $sale->sale_date->format('d/m/Y') }} {{ $sale->created_at->format('H:i') }}</td></tr>
                <tr><td>Kasir</td><td>:</td><td>{{ $sale->user?->name }}</td></tr>
                @if($sale->buyer_name)
                <tr><td>Pembeli</td><td>:</td><td><b>{{ $sale->buyer_name }}</b></td></tr>
                @endif
                <tr><td>Bayar</td><td>:</td><td>{{ $sale->payment_method_label }}</td></tr>
            </table>
            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            @foreach($sale->items as $item)
            <div style="font-size:10px; margin-bottom:4px;">
                <div style="font-weight:bold;">{{ $item->product->name }}</div>
                <div style="display:flex; justify-content:space-between; padding-left:4px;">
                    <span>{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }} {{ $item->unit->unit_name }} &times; {{ number_format($item->sell_price,0,',','.') }}</span>
                    <span style="font-weight:bold;">{{ number_format($item->subtotal,0,',','.') }}</span>
                </div>
            </div>
            @endforeach

            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            <table style="width:100%; font-size:10px; border-collapse:collapse;">
                @if($sale->tax_amount > 0)
                <tr><td>Subtotal</td><td style="text-align:right;">Rp {{ number_format($sale->subtotal_before_tax,0,',','.') }}</td></tr>
                <tr><td>PPN {{ number_format($sale->tax_rate,0) }}%</td><td style="text-align:right;">Rp {{ number_format($sale->tax_amount,0,',','.') }}</td></tr>
                @endif
                <tr style="font-weight:bold; font-size:12px; border-top:1px solid #000;">
                    <td style="padding-top:2px;">TOTAL</td>
                    <td style="text-align:right; padding-top:2px;">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                </tr>
                @if($sale->payment_method === 'cash')
                <tr><td>Tunai</td><td style="text-align:right;">Rp {{ number_format($sale->paid_amount,0,',','.') }}</td></tr>
                <tr><td style="font-weight:bold;">Kembali</td><td style="text-align:right; font-weight:bold;">Rp {{ number_format($sale->change_amount,0,',','.') }}</td></tr>
                @else
                <tr><td colspan="2" style="text-align:center; font-size:9px; padding-top:3px;">Lunas via {{ $sale->payment_method_label }}</td></tr>
                @endif
            </table>

            <div style="border-bottom:1px dashed #000; margin:5px 0;"></div>
            <div style="text-align:center; font-size:9px;">
                <div>{{ $cfg['struk_footer'] ?? 'Terima kasih atas kunjungan Anda!' }}</div>
                @if($sale->notes)
                <div style="margin-top:3px; font-style:italic;">{{ $sale->notes }}</div>
                @endif
                <div style="margin-top:4px; color:#777;">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- ═══ CETAK A4 ═══ --}}
    <div id="print-a4" style="display:none">
        <div style="font-family:'Courier New', Courier, monospace; font-size:14px; color:#000 !important; line-height: 1.2; -webkit-print-color-adjust: exact; print-color-adjust: exact;">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:12px;">
                <div>
                    <div style="font-size:24px; font-weight:bold; color:#000 !important;">{{ $cfg['toko_nama'] ?? config('app.name') }}</div>
                    @if(!empty($cfg['toko_tagline']))<div style="font-size:12px; margin-top:2px;">{{ $cfg['toko_tagline'] }}</div>@endif
                    @if(!empty($cfg['nota_header']))<div style="font-size:12px; margin-top:2px;">{{ $cfg['nota_header'] }}</div>@endif
                    @if(!empty($cfg['toko_alamat']))<div style="font-size:12px; margin-top:4px;">{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</div>@endif
                    @if(!empty($cfg['toko_telepon']))<div style="font-size:12px;">Telp: {{ $cfg['toko_telepon'] }}</div>@endif
                </div>
                <div style="text-align:right;">
                    <div style="font-size:18px; font-weight:bold; text-transform:uppercase; border:2px solid #000; padding:6px 14px; display:inline-block; letter-spacing:1px; color:#000 !important;">
                        NOTA PENJUALAN
                    </div>
                    <div style="margin-top:8px; font-size:12px; line-height:1.6;">
                        <div><b>No. Faktur</b> : {{ $sale->invoice_number }}</div>
                        <div><b>Tanggal</b>    : {{ $sale->sale_date->translatedFormat('d F Y') }} {{ $sale->created_at->format('H:i') }}</div>
                        <div><b>Kasir</b>      : {{ $sale->user?->name ?? '-' }}</div>
                        <div><b>Bayar</b>      : {{ $sale->payment_method_label }}</div>
                    </div>
                </div>
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:14px; margin-bottom:12px; color:#000 !important;">
                <thead>
                    <tr style="border-top:2px solid #000; border-bottom:2px solid #000;">
                        <th style="padding:7px 8px; text-align:center; width:4%;">No</th>
                        <th style="padding:7px 8px; text-align:left;">Produk</th>
                        <th style="padding:7px 8px; text-align:center; width:10%;">Satuan</th>
                        <th style="padding:7px 8px; text-align:right; width:10%;">Qty</th>
                        <th style="padding:7px 8px; text-align:right; width:18%;">Harga</th>
                        <th style="padding:7px 8px; text-align:right; width:18%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $i => $item)
                    <tr style="border-bottom:1px solid #000;">
                        <td style="padding:6px 8px; text-align:center;">{{ $i + 1 }}</td>
                        <td style="padding:6px 8px;">
                            <div style="font-weight:bold;">{{ $item->product->name }}</div>
                            <div style="font-size:11px;">SKU: {{ $item->product->sku }}</div>
                        </td>
                        <td style="padding:6px 8px; text-align:center;">{{ $item->unit->unit_name }}</td>
                        <td style="padding:6px 8px; text-align:right;">{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }}</td>
                        <td style="padding:6px 8px; text-align:right;">{{ number_format($item->sell_price,0,',','.') }}</td>
                        <td style="padding:6px 8px; text-align:right; font-weight:bold;">{{ number_format($item->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if($sale->tax_amount > 0)
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:12px;">Subtotal</td>
                        <td style="padding:5px 8px; text-align:right; font-size:12px;">{{ number_format($sale->subtotal_before_tax,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:12px;">PPN {{ number_format($sale->tax_rate,0) }}%</td>
                        <td style="padding:5px 8px; text-align:right; font-size:12px; font-weight:bold;">{{ number_format($sale->tax_amount,0,',','.') }}</td>
                    </tr>
                    @endif
                    <tr style="border-top:2px solid #000; border-bottom:2px solid #000;">
                        <td colspan="5" style="padding:8px; text-align:right; font-weight:bold; font-size:16px;">TOTAL</td>
                        <td style="padding:8px; text-align:right; font-weight:bold; font-size:16px;">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                    </tr>
                    @if($sale->payment_method === 'cash')
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:12px;">Dibayar (Tunai)</td>
                        <td style="padding:5px 8px; text-align:right; font-size:12px; font-weight:bold;">{{ number_format($sale->paid_amount,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:12px;">Kembalian</td>
                        <td style="padding:5px 8px; text-align:right; font-size:12px; font-weight:bold;">{{ number_format($sale->change_amount,0,',','.') }}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:12px;">Lunas via</td>
                        <td style="padding:5px 8px; text-align:right; font-size:12px; font-weight:bold;">{{ $sale->payment_method_label }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>

            @if($sale->notes)
            <div style="border:1px solid #000; padding:8px; margin-bottom:12px; font-size:12px;">
                <b>Catatan:</b> {{ $sale->notes }}
            </div>
            @endif

            <div style="display:flex; justify-content:flex-end; margin-top:24px; gap:40px;">
                <div style="text-align:center; font-size:12px; width:160px;">
                    <div style="font-weight:bold;">Kasir</div>
                    <div style="height:60px; border-bottom:1px solid #000; margin:8px 16px 4px;"></div>
                    <div>{{ $sale->user?->name ?? '...........' }}</div>
                </div>
                <div style="text-align:center; font-size:12px; width:160px;">
                    <div style="font-weight:bold;">Pelanggan</div>
                    <div style="height:60px; border-bottom:1px solid #000; margin:8px 16px 4px;"></div>
                    <div>{{ $sale->buyer_name ? '( '.$sale->buyer_name.' )' : '( ........................ )' }}</div>
                </div>
            </div>

            <div style="border-top:1px solid #000; margin-top:20px; padding-top:6px; display:flex; justify-content:space-between; font-size:11px;">
                <span>{{ $cfg['struk_footer'] ?? 'Terima kasih atas kunjungan Anda!' }}</span>
                <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    /* Default layar: sembunyikan area cetak */
    #print-struk, #print-a4 { display: none; }

    @media print {
        /* Sembunyikan semua elemen layar */
        #screen-content, nav, header, .no-print { display: none !important; }

        /* Struk 80mm */
        body.mode-struk #print-struk {
            display: block !important;
        }

        /* A4 */
        body.mode-a4 #print-a4 {
            display: block !important;
        }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    function cetakStruk() {
        // Inject ukuran halaman 80mm
        let ps = document.getElementById('ps-override');
        if (ps) ps.remove();
        ps = document.createElement('style');
        ps.id = 'ps-override';
        ps.textContent = '@media print { @page { size: 80mm auto; margin: 3mm; } }';
        document.head.appendChild(ps);

        document.body.classList.remove('mode-a4');
        document.body.classList.add('mode-struk');
        window.print();
        setTimeout(() => document.body.classList.remove('mode-struk'), 1500);
    }

    function cetakA4() {
        let ps = document.getElementById('ps-override');
        if (ps) ps.remove();
        ps = document.createElement('style');
        ps.id = 'ps-override';
        ps.textContent = '@media print { @page { size: A4 portrait; margin: 12mm 15mm; } }';
        document.head.appendChild(ps);

        document.body.classList.remove('mode-struk');
        document.body.classList.add('mode-a4');
        window.print();
        setTimeout(() => document.body.classList.remove('mode-a4'), 1500);
    }

    @if(request('print'))
    window.addEventListener('load', () => setTimeout(cetakStruk, 400));
    @endif
    </script>
    @endpush

</x-app-layout>
