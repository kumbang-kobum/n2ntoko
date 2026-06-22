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
            <div class="flex flex-wrap gap-2 no-print">
                <button onclick="cetakStruk()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Struk 80mm
                </button>
                <button onclick="cetakLX310()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-800 text-gray-800 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m0 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8m6 0v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4"/>
                    </svg>
                    LX-310 (2-ply)
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

    {{-- ═══ MODAL CETAK ═══ --}}
    @if(request('cetak'))
    <div x-data="{ open: true }" x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 no-print"
         style="background:rgba(0,0,0,0.55)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xs text-center overflow-hidden">
            {{-- Header hijau --}}
            <div class="bg-green-500 px-6 pt-7 pb-5">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-white font-bold text-lg leading-tight">Transaksi Berhasil!</p>
                <p class="text-green-100 text-sm mt-1 font-mono">{{ $sale->invoice_number }}</p>
            </div>

            {{-- Tombol cetak --}}
            <div class="p-5 space-y-3">
                <p class="text-xs text-gray-500 mb-4">Pilih format nota yang ingin dicetak:</p>

                <button @click="open=false; $nextTick(()=>cetakStruk())"
                        class="w-full py-3.5 bg-gray-800 text-white font-semibold rounded-xl
                               hover:bg-gray-900 active:bg-black transition flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Nota Kecil (Thermal 80mm)
                </button>

                <button @click="open=false; $nextTick(()=>cetakLX310())"
                        class="w-full py-3.5 border-2 border-gray-700 text-gray-700 font-semibold rounded-xl
                               hover:bg-gray-50 active:bg-gray-100 transition flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m0 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8m6 0v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4"/>
                    </svg>
                    Nota Besar (2-ply LX-310)
                </button>

                <button @click="open=false"
                        class="w-full py-2.5 text-sm text-gray-400 hover:text-gray-600 transition">
                    Lewati, tidak cetak
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ STRUK 80mm ═══ --}}
    <div id="print-struk" style="display:none">
        <div style="font-family:Tahoma,Arial,sans-serif; font-size:11px; color:#000; background:#fff; width:100%; line-height:1.4;">

            {{-- Header --}}
            <div style="text-align:center; margin-bottom:3px;">
                <div style="font-size:14px; font-weight:bold;">{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</div>
                @if(!empty($cfg['toko_tagline']))<div>{{ $cfg['toko_tagline'] }}</div>@endif
                @if(!empty($cfg['nota_header']))<div>{{ $cfg['nota_header'] }}</div>@endif
                @if(!empty($cfg['toko_alamat']))<div>{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</div>@endif
                @if(!empty($cfg['toko_telepon']))<div>Telp: {{ $cfg['toko_telepon'] }}</div>@endif
            </div>
            <div style="border-bottom:1px dashed #000; margin:3px 0;"></div>
            <div style="text-align:center; font-weight:bold; margin-bottom:2px;">NOTA PENJUALAN</div>
            <div style="border-bottom:1px dashed #000; margin:3px 0;"></div>

            {{-- Info --}}
            <table style="width:100%; border-collapse:collapse; font-size:11px;">
                <tr><td style="width:36%; padding:0;">No</td><td style="width:4%; padding:0;">:</td><td style="padding:0;">{{ $sale->invoice_number }}</td></tr>
                <tr><td style="padding:0;">Tanggal</td><td style="padding:0;">:</td><td style="padding:0;">{{ $sale->sale_date->format('d/m/Y') }} {{ $sale->created_at->format('H:i') }}</td></tr>
                <tr><td style="padding:0;">Kasir</td><td style="padding:0;">:</td><td style="padding:0;">{{ $sale->user?->name }}</td></tr>
                @if($sale->buyer_name)
                <tr><td style="padding:0;">Pembeli</td><td style="padding:0;">:</td><td style="padding:0;">{{ $sale->buyer_name }}</td></tr>
                @endif
                <tr><td style="padding:0;">Bayar</td><td style="padding:0;">:</td><td style="padding:0;">{{ $sale->payment_method_label }}</td></tr>
            </table>
            <div style="border-bottom:1px dashed #000; margin:3px 0;"></div>

            {{-- Item produk --}}
            @foreach($sale->items as $item)
            <div style="margin-bottom:2px;">
                <div>{{ $item->product->name }}</div>
                <table style="width:100%; border-collapse:collapse; font-size:11px;">
                    <tr>
                        <td style="padding:0 0 0 6px;">{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }} {{ $item->unit->unit_name }} x {{ number_format($item->sell_price,0,',','.') }}</td>
                        <td style="padding:0; text-align:right; white-space:nowrap;">{{ number_format($item->subtotal,0,',','.') }}</td>
                    </tr>
                </table>
            </div>
            @endforeach
            <div style="border-bottom:1px dashed #000; margin:3px 0;"></div>

            {{-- Totals --}}
            <table style="width:100%; border-collapse:collapse; font-size:11px;">
                @if($sale->tax_amount > 0)
                <tr><td style="padding:0;">Subtotal</td><td style="padding:0; text-align:right; white-space:nowrap;">Rp {{ number_format($sale->subtotal_before_tax,0,',','.') }}</td></tr>
                <tr><td style="padding:0;">PPN {{ number_format($sale->tax_rate,0) }}%</td><td style="padding:0; text-align:right; white-space:nowrap;">Rp {{ number_format($sale->tax_amount,0,',','.') }}</td></tr>
                @endif
                <tr>
                    <td style="padding:2px 0; font-size:13px; font-weight:bold;">TOTAL</td>
                    <td style="padding:2px 0; font-size:13px; font-weight:bold; text-align:right; white-space:nowrap;">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                </tr>
                @if($sale->payment_method === 'cash')
                <tr><td style="padding:0;">Tunai</td><td style="padding:0; text-align:right; white-space:nowrap;">Rp {{ number_format($sale->paid_amount,0,',','.') }}</td></tr>
                <tr><td style="padding:0;">Kembali</td><td style="padding:0; text-align:right; white-space:nowrap;">Rp {{ number_format($sale->change_amount,0,',','.') }}</td></tr>
                @else
                <tr><td colspan="2" style="padding:0; text-align:center; font-size:10px;">Lunas via {{ $sale->payment_method_label }}</td></tr>
                @endif
            </table>
            <div style="border-bottom:1px dashed #000; margin:3px 0;"></div>

            {{-- Footer --}}
            <div style="text-align:center; font-size:10px;">
                <div>{{ $cfg['struk_footer'] ?? 'Terima kasih atas kunjungan Anda!' }}</div>
                @if($sale->notes)<div>{{ $sale->notes }}</div>@endif
                <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
            </div>

        </div>
    </div>

    {{-- ═══ CETAK LX-310 (dot matrix 2-ply) ═══ --}}
    <div id="print-lx310" style="display:none">
        {{-- Font Tahoma seperti referensi LaporanBilling.php — bukan Courier, bukan bold --}}
        <table width="100%" bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" style="font-family:Tahoma,Arial,sans-serif; font-size:12px; color:#000000;">

            {{-- Header toko --}}
            <tr><td colspan="2" align="center" style="padding-bottom:2px; border-bottom:1px solid #000;">
                <font face="Tahoma" size="3" color="000000"><b>{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</b></font><br>
                @if(!empty($cfg['toko_tagline']))<font face="Tahoma" size="1" color="000000">{{ $cfg['toko_tagline'] }}</font><br>@endif
                @if(!empty($cfg['nota_header']))<font face="Tahoma" size="1" color="000000">{{ $cfg['nota_header'] }}</font><br>@endif
                @if(!empty($cfg['toko_alamat']))<font face="Tahoma" size="1" color="000000">{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</font><br>@endif
                @if(!empty($cfg['toko_telepon']))<font face="Tahoma" size="1" color="000000">Telp: {{ $cfg['toko_telepon'] }}</font><br>@endif
                <font face="Tahoma" size="1" color="000000">====  NOTA PENJUALAN  ====</font>
            </td></tr>

            {{-- Info transaksi 2 kolom --}}
            <tr>
                <td width="50%" valign="top" style="padding:1px 4px 0 0; border-right:1px solid #000;">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr><td width="80"><font face="Tahoma" size="1" color="000000">No. Faktur</font></td><td width="8"><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $sale->invoice_number }}</font></td></tr>
                        <tr><td><font face="Tahoma" size="1" color="000000">Tanggal</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $sale->sale_date->format('d/m/Y') }} {{ $sale->created_at->format('H:i') }}</font></td></tr>
                    </table>
                </td>
                <td width="50%" valign="top" style="padding:1px 0 0 4px;">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr><td width="60"><font face="Tahoma" size="1" color="000000">Kasir</font></td><td width="8"><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $sale->user?->name ?? '-' }}</font></td></tr>
                        @if($sale->buyer_name)<tr><td><font face="Tahoma" size="1" color="000000">Pembeli</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $sale->buyer_name }}</font></td></tr>@endif
                        <tr><td><font face="Tahoma" size="1" color="000000">Bayar</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $sale->payment_method_label }}</font></td></tr>
                    </table>
                </td>
            </tr>

            {{-- Garis pemisah --}}
            <tr><td colspan="2" style="border-bottom:1px solid #000; padding:1px 0;"></td></tr>

            {{-- Tabel item --}}
            <tr><td colspan="2" style="padding:0;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr style="border-bottom:1px solid #000;">
                        <td width="4%" align="center" style="border-bottom:1px solid #000;"><font face="Tahoma" size="1" color="000000">No</font></td>
                        <td style="border-bottom:1px solid #000;"><font face="Tahoma" size="1" color="000000">Nama Produk</font></td>
                        <td width="8%" align="center" style="border-bottom:1px solid #000;"><font face="Tahoma" size="1" color="000000">Sat</font></td>
                        <td width="7%" align="right" style="border-bottom:1px solid #000;"><font face="Tahoma" size="1" color="000000">Qty</font></td>
                        <td width="19%" align="right" style="border-bottom:1px solid #000;"><font face="Tahoma" size="1" color="000000">Harga</font></td>
                        <td width="19%" align="right" style="border-bottom:1px solid #000;"><font face="Tahoma" size="1" color="000000">Subtotal</font></td>
                    </tr>
                    @foreach($sale->items as $i => $item)
                    <tr>
                        <td align="center" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ $i+1 }}</font></td>
                        <td style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ $item->product->name }}</font></td>
                        <td align="center" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ $item->unit->unit_name }}</font></td>
                        <td align="right" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }}</font></td>
                        <td align="right" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ number_format($item->sell_price,0,',','.') }}</font></td>
                        <td align="right" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ number_format($item->subtotal,0,',','.') }}</font></td>
                    </tr>
                    @endforeach
                </table>
            </td></tr>

            <tr><td colspan="2" style="border-bottom:1px solid #000; padding:1px 0;"></td></tr>

            {{-- Totals kanan + Catatan kiri --}}
            <tr>
                <td width="50%" valign="top" style="padding:1px 4px 0 0; font-size:11px;">
                    @if($sale->notes)<font face="Tahoma" size="1" color="000000">Catatan: {{ $sale->notes }}</font>@endif
                </td>
                <td width="50%" valign="top" style="padding:1px 0 0 0;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        @if($sale->tax_amount > 0)
                        <tr><td><font face="Tahoma" size="1" color="000000">Subtotal</font></td><td align="right"><font face="Tahoma" size="1" color="000000">{{ number_format($sale->subtotal_before_tax,0,',','.') }}</font></td></tr>
                        <tr><td><font face="Tahoma" size="1" color="000000">PPN {{ number_format($sale->tax_rate,0) }}%</font></td><td align="right"><font face="Tahoma" size="1" color="000000">{{ number_format($sale->tax_amount,0,',','.') }}</font></td></tr>
                        @endif
                        <tr style="border-top:1px solid #000;">
                            <td><font face="Tahoma" size="2" color="000000"><b>TOTAL</b></font></td>
                            <td align="right"><font face="Tahoma" size="2" color="000000"><b>Rp {{ number_format($sale->total_amount,0,',','.') }}</b></font></td>
                        </tr>
                        @if($sale->payment_method === 'cash')
                        <tr><td><font face="Tahoma" size="1" color="000000">Tunai</font></td><td align="right"><font face="Tahoma" size="1" color="000000">Rp {{ number_format($sale->paid_amount,0,',','.') }}</font></td></tr>
                        <tr><td><font face="Tahoma" size="1" color="000000">Kembali</font></td><td align="right"><font face="Tahoma" size="1" color="000000">Rp {{ number_format($sale->change_amount,0,',','.') }}</font></td></tr>
                        @else
                        <tr><td colspan="2" align="center"><font face="Tahoma" size="1" color="000000">Lunas via {{ $sale->payment_method_label }}</font></td></tr>
                        @endif
                    </table>
                </td>
            </tr>

            {{-- TTD --}}
            <tr><td colspan="2" style="border-top:1px solid #000; padding-top:2px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="50%">&nbsp;</td>
                        <td width="25%" align="center"><font face="Tahoma" size="1" color="000000">Kasir</font></td>
                        <td width="25%" align="center"><font face="Tahoma" size="1" color="000000">Pelanggan</font></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td height="24" style="border-bottom:1px solid #000; margin:0 8px;">&nbsp;</td>
                        <td height="24" style="border-bottom:1px solid #000; margin:0 8px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="center"><font face="Tahoma" size="1" color="000000">{{ $sale->user?->name ?? '...........' }}</font></td>
                        <td align="center"><font face="Tahoma" size="1" color="000000">{{ $sale->buyer_name ? '('.$sale->buyer_name.')' : '(..............)' }}</font></td>
                    </tr>
                </table>
            </td></tr>

            {{-- Footer --}}
            <tr><td colspan="2" style="border-top:1px solid #000; padding-top:2px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><font face="Tahoma" size="1" color="000000">{{ $cfg['struk_footer'] ?? 'Terima kasih atas kunjungan Anda!' }}</font></td>
                        <td align="right"><font face="Tahoma" size="1" color="000000">Dicetak: {{ now()->format('d/m/Y H:i') }}</font></td>
                    </tr>
                </table>
            </td></tr>

        </table>
    </div>

    {{-- ═══ CETAK A4 ═══ --}}
    <div id="print-a4" style="display:none">
        <div class="nota-a4-print" style="font-family:'Courier New', Courier, monospace; font-size:14px; font-weight:700; color:#000 !important; background:#fff !important; line-height:1.2; -webkit-print-color-adjust: exact; print-color-adjust: exact;">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:12px;">
                <div>
                    <div style="font-size:24px; font-weight:bold; color:#000 !important;">{{ $cfg['toko_nama'] ?? config('app.name') }}</div>
                    @if(!empty($cfg['toko_tagline']))<div style="font-size:13px; margin-top:2px; font-weight:bold;">{{ $cfg['toko_tagline'] }}</div>@endif
                    @if(!empty($cfg['nota_header']))<div style="font-size:13px; margin-top:2px;">{{ $cfg['nota_header'] }}</div>@endif
                    @if(!empty($cfg['toko_alamat']))<div style="font-size:13px; margin-top:4px;">{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</div>@endif
                    @if(!empty($cfg['toko_telepon']))<div style="font-size:13px;">Telp: {{ $cfg['toko_telepon'] }}</div>@endif
                </div>
                <div style="text-align:right;">
                    <div style="font-size:18px; font-weight:bold; text-transform:uppercase; border:2px solid #000; padding:6px 14px; display:inline-block; letter-spacing:1px; color:#000 !important;">
                        NOTA PENJUALAN
                    </div>
                    <div style="margin-top:8px; font-size:13px; line-height:1.6; color:#000 !important;">
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
                        <th style="padding:7px 8px; text-align:center; width:4%; color:#000 !important;">No</th>
                        <th style="padding:7px 8px; text-align:left; color:#000 !important;">Produk</th>
                        <th style="padding:7px 8px; text-align:center; width:10%; color:#000 !important;">Satuan</th>
                        <th style="padding:7px 8px; text-align:right; width:10%; color:#000 !important;">Qty</th>
                        <th style="padding:7px 8px; text-align:right; width:18%; color:#000 !important;">Harga</th>
                        <th style="padding:7px 8px; text-align:right; width:18%; color:#000 !important;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $i => $item)
                    <tr style="border-bottom:1px solid #000;">
                        <td style="padding:6px 8px; text-align:center; color:#000 !important; font-weight:bold;">{{ $i + 1 }}</td>
                        <td style="padding:6px 8px; color:#000 !important;">
                            <div style="font-weight:bold;">{{ $item->product->name }}</div>
                        </td>
                        <td style="padding:6px 8px; text-align:center; color:#000 !important;">{{ $item->unit->unit_name }}</td>
                        <td style="padding:6px 8px; text-align:right; color:#000 !important; font-weight:bold;">{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }}</td>
                        <td style="padding:6px 8px; text-align:right; color:#000 !important;">{{ number_format($item->sell_price,0,',','.') }}</td>
                        <td style="padding:6px 8px; text-align:right; font-weight:bold; color:#000 !important;">{{ number_format($item->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if($sale->tax_amount > 0)
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:13px; color:#000 !important;">Subtotal</td>
                        <td style="padding:5px 8px; text-align:right; font-size:13px; color:#000 !important;">{{ number_format($sale->subtotal_before_tax,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:13px; color:#000 !important;">PPN {{ number_format($sale->tax_rate,0) }}%</td>
                        <td style="padding:5px 8px; text-align:right; font-size:13px; font-weight:bold; color:#000 !important;">{{ number_format($sale->tax_amount,0,',','.') }}</td>
                    </tr>
                    @endif
                    <tr style="border-top:2px solid #000; border-bottom:2px solid #000;">
                        <td colspan="5" style="padding:8px; text-align:right; font-weight:bold; font-size:16px; color:#000 !important;">TOTAL</td>
                        <td style="padding:8px; text-align:right; font-weight:bold; font-size:16px; color:#000 !important;">Rp {{ number_format($sale->total_amount,0,',','.') }}</td>
                    </tr>
                    @if($sale->payment_method === 'cash')
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:13px; color:#000 !important;">Dibayar (Tunai)</td>
                        <td style="padding:5px 8px; text-align:right; font-size:13px; font-weight:bold; color:#000 !important;">{{ number_format($sale->paid_amount,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:13px; color:#000 !important;">Kembalian</td>
                        <td style="padding:5px 8px; text-align:right; font-size:13px; font-weight:bold; color:#000 !important;">{{ number_format($sale->change_amount,0,',','.') }}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="5" style="padding:5px 8px; text-align:right; font-size:13px; color:#000 !important;">Lunas via</td>
                        <td style="padding:5px 8px; text-align:right; font-size:13px; font-weight:bold; color:#000 !important;">{{ $sale->payment_method_label }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>

            @if($sale->notes)
            <div style="border:1px solid #000; padding:8px; margin-bottom:12px; font-size:13px; color:#000 !important;">
                <b>Catatan:</b> {{ $sale->notes }}
            </div>
            @endif

            <div style="display:flex; justify-content:flex-end; margin-top:10px; gap:32px;">
                <div style="text-align:center; font-size:13px; width:160px; color:#000 !important;">
                    <div style="font-weight:bold;">Kasir</div>
                    <div style="height:32px; border-bottom:1px solid #000; margin:5px 16px 3px;"></div>
                    <div>{{ $sale->user?->name ?? '...........' }}</div>
                </div>
                <div style="text-align:center; font-size:13px; width:160px; color:#000 !important;">
                    <div style="font-weight:bold;">Pelanggan</div>
                    <div style="height:32px; border-bottom:1px solid #000; margin:5px 16px 3px;"></div>
                    <div>{{ $sale->buyer_name ? '( '.$sale->buyer_name.' )' : '( ........................ )' }}</div>
                </div>
            </div>

            <div style="border-top:1px solid #000; margin-top:10px; padding-top:4px; display:flex; justify-content:space-between; font-size:12px; color:#000 !important;">
                <span>{{ $cfg['struk_footer'] ?? 'Terima kasih atas kunjungan Anda!' }}</span>
                <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    /* Default layar: sembunyikan area cetak */
    #print-struk, #print-a4, #print-lx310 { display: none; }

    @media print {
        /* Sembunyikan semua elemen layar */
        #screen-content, nav, header, .no-print { display: none !important; }
        html,
        body,
        body.mode-a4,
        body.mode-a4 > div,
        body.mode-a4 main {
            background: #fff !important;
        }
        body.mode-a4 > div {
            min-height: 0 !important;
        }
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Struk 80mm */
        body.mode-struk {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body.mode-struk * {
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
        }
        body.mode-struk #print-struk {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body.mode-struk #print-struk > div {
            background: #fff !important;
            background-color: #fff !important;
        }
        body.mode-struk #print-struk * {
            color: #000 !important;
        }

        /* A4 */
        body.mode-a4 #print-a4 {
            display: block !important;
        }
        body.mode-a4 #print-a4,
        body.mode-a4 #print-a4 * {
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
            color: #000 !important;
            text-shadow: none !important;
        }

        /* LX-310 — layout khusus dot matrix */
        body.mode-lx310 {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body.mode-lx310 * {
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
        }
        body.mode-lx310 #print-lx310 {
            display: block !important;
        }
        body.mode-lx310 #print-lx310 > div {
            background: #fff !important;
        }
        body.mode-lx310 #print-lx310 * {
            color: #000 !important;
            text-shadow: none !important;
        }
        body.mode-a4 #print-a4 .nota-a4-print {
            background: #fff !important;
            color: #000 !important;
            font-size: 12pt !important;
            font-weight: 700 !important;
            line-height: 1.08 !important;
        }
        body.mode-a4 #print-a4 .nota-a4-print table {
            margin-bottom: 6px !important;
        }
        body.mode-a4 #print-a4 th,
        body.mode-a4 #print-a4 td {
            color: #000 !important;
            font-weight: 700;
            line-height: 1.05 !important;
            padding: 2px 5px !important;
        }
        body.mode-a4 #print-a4 thead th {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
        body.mode-a4 #print-a4 tfoot td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }
        body.mode-a4 #print-a4 table,
        body.mode-a4 #print-a4 tr,
        body.mode-a4 #print-a4 th,
        body.mode-a4 #print-a4 td {
            border-color: #000 !important;
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

        document.body.classList.remove('mode-a4', 'mode-lx310');
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

        document.body.classList.remove('mode-struk', 'mode-lx310');
        document.body.classList.add('mode-a4');
        window.print();
        setTimeout(() => document.body.classList.remove('mode-a4'), 1500);
    }

    function cetakLX310() {
        let ps = document.getElementById('ps-override');
        if (ps) ps.remove();
        ps = document.createElement('style');
        ps.id = 'ps-override';
        // Driver LX-310 di-set A4, gunakan A4 agar tidak ada scaling paksa dari browser
        ps.textContent = '@media print { @page { size: A4 portrait; margin: 10mm 15mm; } }';
        document.head.appendChild(ps);

        document.body.classList.remove('mode-struk', 'mode-a4');
        document.body.classList.add('mode-lx310');
        window.print();
        setTimeout(() => document.body.classList.remove('mode-lx310'), 1500);
    }

    </script>
    @endpush

</x-app-layout>
