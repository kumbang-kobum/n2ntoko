<x-app-layout>
    @php
        $badgeColors = ['draft'=>'yellow','ordered'=>'blue','confirmed'=>'green','paid'=>'gray'];
        $badgeLabels = ['draft'=>'Draft','ordered'=>'Dipesan','confirmed'=>'Diterima','paid'=>'Lunas'];
        $bc = $badgeColors[$purchase->status] ?? 'gray';
        $bl = $badgeLabels[$purchase->status] ?? $purchase->status;
        $cfg = \App\Models\Setting::all();
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-xl font-semibold text-gray-800">{{ $purchase->invoice_number }}</h2>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $bc }}-100 text-{{ $bc }}-700">
                    {{ $bl }}
                </span>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-2 no-print">
                {{-- Tombol Cetak --}}
                <button onclick="cetakStruk()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 active:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Struk 80mm
                </button>
                <button onclick="openLX310Popup()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-800 text-gray-800 text-sm rounded-lg hover:bg-gray-50 active:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m0 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8m6 0v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4"/>
                    </svg>
                    LX-310 (2-ply)
                </button>

                @if(in_array($purchase->status, ['draft', 'ordered']))
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
                @endif
            </div>
        </div>
    </x-slot>

    <div id="screen-content" class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
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
                        <p class="font-semibold text-gray-800">{{ $purchase->purchase_date->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Supplier</p>
                        <p class="font-semibold text-gray-800">{{ $purchase->supplier?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dibuat oleh</p>
                        <p class="font-semibold text-gray-800">{{ $purchase->user?->name ?? '-' }}</p>
                    </div>
                    @if($purchase->buyer_name)
                    <div>
                        <p class="text-xs text-gray-500">Nama Pembeli</p>
                        <p class="font-semibold text-gray-800">{{ $purchase->buyer_name }}</p>
                    </div>
                    @endif
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
                                    {{ rtrim(rtrim(number_format($item->qty, 2, ',', '.'), '0'), ',') }}
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
                            @if($purchase->paid_amount > 0)
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Sudah Dibayar</td>
                                <td class="px-4 py-2 text-right text-green-700 font-medium">Rp {{ number_format($purchase->paid_amount, 0, ',', '.') }}</td>
                            </tr>
                            @if($purchase->hutang > 0)
                            <tr class="text-sm">
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Sisa Hutang</td>
                                <td class="px-4 py-2 text-right text-orange-600 font-bold">Rp {{ number_format($purchase->hutang, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Bayar Hutang --}}
            @if($purchase->status === 'confirmed' && $purchase->hutang > 0)
            @can('pembelian.edit')
            <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-5" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-orange-700">Sisa Hutang ke Supplier</p>
                        <p class="text-2xl font-bold text-orange-600 mt-0.5">Rp {{ number_format($purchase->hutang, 0, ',', '.') }}</p>
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
                                <input type="number" name="bayar" value="{{ old('bayar', $purchase->hutang) }}"
                                       min="1" max="{{ $purchase->hutang }}" step="1"
                                       class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-orange-500">                            </div>
                            @error('bayar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" onclick="return confirm('Konfirmasi pembayaran ini?')"
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

    {{-- ═══ STRUK 80mm ═══ --}}
    <div id="print-struk" style="display:none">
        <div style="font-family:'Courier New',monospace; font-size:12px; width:72mm; margin:0 auto; line-height:1.45; color:#000 !important; -webkit-print-color-adjust: exact;">

            <div style="text-align:center; margin-bottom:5px;">
                <div style="font-size:16px; font-weight:bold; letter-spacing:1px; color:#000 !important;">{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</div>
                @if(!empty($cfg['toko_tagline']))<div style="font-size:11px; font-weight:bold;">{{ $cfg['toko_tagline'] }}</div>@endif
                @if(!empty($cfg['nota_header']))<div style="font-size:11px;">{{ $cfg['nota_header'] }}</div>@endif
                @if(!empty($cfg['toko_alamat']))<div style="font-size:11px;">{{ $cfg['toko_alamat'] }}{{ !empty($cfg['toko_kota']) ? ', '.$cfg['toko_kota'] : '' }}</div>@endif
                @if(!empty($cfg['toko_telepon']))<div style="font-size:11px;">Telp: {{ $cfg['toko_telepon'] }}</div>@endif
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
                <div style="font-size:11px; font-weight:bold;">NOTA PEMBELIAN</div>
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
            </div>

            <table style="width:100%; font-size:11px; border-collapse:collapse; margin-bottom:4px; color:#000 !important;">
                <tr><td style="width:38%">No. Faktur</td><td style="width:4%">:</td><td><b>{{ $purchase->invoice_number }}</b></td></tr>
                <tr><td>Tanggal</td><td>:</td><td>{{ $purchase->purchase_date->format('d/m/Y') }}</td></tr>
                <tr><td>Supplier</td><td>:</td><td>{{ $purchase->supplier?->name ?? '-' }}</td></tr>
                @if($purchase->buyer_name)<tr><td>Pembeli</td><td>:</td><td><b>{{ $purchase->buyer_name }}</b></td></tr>@endif
                <tr><td>Status</td><td>:</td><td>{{ strtoupper($bl) }}</td></tr>
                <tr><td>Dibuat</td><td>:</td><td>{{ $purchase->user?->name }}</td></tr>
            </table>
            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            @foreach($purchase->items as $item)
            <div style="font-size:11px; margin-bottom:4px; color:#000 !important;">
                <div style="font-weight:bold;">{{ $item->product->name }}</div>
                <div style="display:flex; justify-content:space-between; padding-left:4px;">
                    <span>{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }} {{ $item->unit->unit_name }} &times; {{ number_format($item->buy_price,0,',','.') }}</span>
                    <span style="font-weight:bold;">{{ number_format($item->subtotal,0,',','.') }}</span>
                </div>
            </div>
            @endforeach

            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            <table style="width:100%; font-size:11px; border-collapse:collapse; color:#000 !important;">
                <tr style="font-weight:bold; font-size:13px;">
                    <td style="padding-top:2px;">TOTAL</td>
                    <td style="text-align:right; padding-top:2px;">Rp {{ number_format($purchase->total_amount,0,',','.') }}</td>
                </tr>
                @if($purchase->paid_amount > 0)
                <tr><td>Dibayar</td><td style="text-align:right;">Rp {{ number_format($purchase->paid_amount,0,',','.') }}</td></tr>
                @endif
                @if($purchase->hutang > 0)
                <tr style="font-weight:bold;"><td>Sisa Hutang</td><td style="text-align:right;">Rp {{ number_format($purchase->hutang,0,',','.') }}</td></tr>
                @endif
            </table>

            @if($purchase->notes)
            <div style="border-top:1px dashed #000; margin-top:5px; padding-top:4px; font-size:11px; color:#000 !important; font-weight:bold;">
                Catatan: {{ $purchase->notes }}
            </div>
            @endif

            <div style="border-top:1px dashed #000; margin-top:8px; padding-top:6px; font-size:11px; color:#000 !important;">
                <div style="display:flex; justify-content:space-between;">
                    <div style="text-align:center; width:45%;">
                        <div>Penerima</div>
                        <div style="height:35px; border-bottom:1px solid #000; margin-top:5px;"></div>
                    </div>
                    <div style="text-align:center; width:45%;">
                        <div>Supplier</div>
                        <div style="height:35px; border-bottom:1px solid #000; margin-top:5px;"></div>
                    </div>
                </div>
            </div>
            <div style="text-align:center; font-size:11px; margin-top:8px; color:#000 !important; font-weight:bold;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- ═══ CETAK LX-310 (dot matrix 2-ply) ═══ --}}
    <div id="print-lx310" style="display:none">
        <table width="100%" bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" style="font-family:Tahoma,Arial,sans-serif; font-size:12px; color:#000000;">

            {{-- Header toko --}}
            <tr><td colspan="2" align="center" style="padding-bottom:2px; border-bottom:1px solid #000;">
                <font face="Tahoma" size="3" color="000000"><b>{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</b></font><br>
                @if(!empty($cfg['toko_tagline']))<font face="Tahoma" size="1" color="000000">{{ $cfg['toko_tagline'] }}</font><br>@endif
                @if(!empty($cfg['nota_header']))<font face="Tahoma" size="1" color="000000">{{ $cfg['nota_header'] }}</font><br>@endif
                @if(!empty($cfg['toko_alamat']))<font face="Tahoma" size="1" color="000000">{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</font><br>@endif
                @if(!empty($cfg['toko_telepon']))<font face="Tahoma" size="1" color="000000">Telp: {{ $cfg['toko_telepon'] }}</font><br>@endif
                <font face="Tahoma" size="1" color="000000">====  {{ $purchase->status === 'draft' ? 'PURCHASE ORDER' : ($purchase->status === 'ordered' ? 'SURAT PESANAN' : 'NOTA PEMBELIAN') }}  ====</font>
            </td></tr>

            {{-- Info transaksi 2 kolom --}}
            <tr>
                <td width="50%" valign="top" style="padding:1px 4px 0 0; border-right:1px solid #000;">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr><td width="80"><font face="Tahoma" size="1" color="000000">No. Faktur</font></td><td width="8"><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $purchase->invoice_number }}</font></td></tr>
                        <tr><td><font face="Tahoma" size="1" color="000000">Tanggal</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $purchase->purchase_date->format('d/m/Y') }} {{ $purchase->created_at->format('H:i') }}</font></td></tr>
                    </table>
                </td>
                <td width="50%" valign="top" style="padding:1px 0 0 4px;">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr><td width="64"><font face="Tahoma" size="1" color="000000">Supplier</font></td><td width="8"><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $purchase->supplier?->name ?? '-' }}</font></td></tr>
                        <tr><td><font face="Tahoma" size="1" color="000000">Dibuat</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $purchase->user?->name ?? '-' }}</font></td></tr>
                        @if($purchase->buyer_name)<tr><td><font face="Tahoma" size="1" color="000000">Pembeli</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $purchase->buyer_name }}</font></td></tr>@endif
                        <tr><td><font face="Tahoma" size="1" color="000000">Status</font></td><td><font face="Tahoma" size="1" color="000000">:</font></td><td><font face="Tahoma" size="1" color="000000">{{ $bl }}</font></td></tr>
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
                    @foreach($purchase->items as $i => $item)
                    <tr>
                        <td align="center" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ $i+1 }}</font></td>
                        <td style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ $item->product->name }}</font></td>
                        <td align="center" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ $item->unit->unit_name }}</font></td>
                        <td align="right" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }}</font></td>
                        <td align="right" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ number_format($item->buy_price,0,',','.') }}</font></td>
                        <td align="right" style="border-bottom:1px dashed #ccc;"><font face="Tahoma" size="1" color="000000">{{ number_format($item->subtotal,0,',','.') }}</font></td>
                    </tr>
                    @endforeach
                </table>
            </td></tr>

            <tr><td colspan="2" style="border-bottom:1px solid #000; padding:1px 0;"></td></tr>

            {{-- Totals kanan + Catatan kiri --}}
            <tr>
                <td width="50%" valign="top" style="padding:1px 4px 0 0; font-size:11px;">
                    @if($purchase->notes)<font face="Tahoma" size="1" color="000000">Catatan: {{ $purchase->notes }}</font>@endif
                </td>
                <td width="50%" valign="top" style="padding:1px 0 0 0;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr style="border-top:1px solid #000;">
                            <td><font face="Tahoma" size="2" color="000000"><b>TOTAL</b></font></td>
                            <td align="right"><font face="Tahoma" size="2" color="000000"><b>Rp {{ number_format($purchase->total_amount,0,',','.') }}</b></font></td>
                        </tr>
                        @if($purchase->paid_amount > 0)
                        <tr><td><font face="Tahoma" size="1" color="000000">Dibayar</font></td><td align="right"><font face="Tahoma" size="1" color="000000">Rp {{ number_format($purchase->paid_amount,0,',','.') }}</font></td></tr>
                        @endif
                        @if($purchase->hutang > 0)
                        <tr><td><font face="Tahoma" size="1" color="000000">Sisa Hutang</font></td><td align="right"><font face="Tahoma" size="1" color="000000">Rp {{ number_format($purchase->hutang,0,',','.') }}</font></td></tr>
                        @elseif($purchase->status === 'paid')
                        <tr><td colspan="2" align="center"><font face="Tahoma" size="1" color="000000">Lunas</font></td></tr>
                        @endif
                    </table>
                </td>
            </tr>

            {{-- TTD --}}
            <tr><td colspan="2" style="border-top:1px solid #000; padding-top:2px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="34%" align="center"><font face="Tahoma" size="1" color="000000">Dibuat</font></td>
                        <td width="33%" align="center"><font face="Tahoma" size="1" color="000000">Penerima</font></td>
                        <td width="33%" align="center"><font face="Tahoma" size="1" color="000000">Supplier</font></td>
                    </tr>
                    <tr>
                        <td height="24" style="border-bottom:1px solid #000; margin:0 8px;">&nbsp;</td>
                        <td height="24" style="border-bottom:1px solid #000; margin:0 8px;">&nbsp;</td>
                        <td height="24" style="border-bottom:1px solid #000; margin:0 8px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center"><font face="Tahoma" size="1" color="000000">{{ $purchase->user?->name ?? '...........' }}</font></td>
                        <td align="center"><font face="Tahoma" size="1" color="000000">(..............)</font></td>
                        <td align="center"><font face="Tahoma" size="1" color="000000">{{ $purchase->supplier?->name ? '('.$purchase->supplier->name.')' : '(..............)' }}</font></td>
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

    {{-- ═══ POPUP PILIH FORMAT LX-310 ═══ --}}
    <div id="lx310-popup"
         onclick="if(event.target===this)closeLX310Popup()"
         style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center; padding:1rem;"
         class="no-print">
        <div style="background:#fff; border-radius:1rem; box-shadow:0 20px 60px rgba(0,0,0,.3); width:100%; max-width:320px; overflow:hidden;">
            <div style="background:#1f2937; padding:1.25rem 1.5rem;">
                <p style="color:#fff; font-weight:700; font-size:1rem; margin:0;">Cetak LX-310 (2-ply)</p>
                <p style="color:#9ca3af; font-size:0.75rem; margin:4px 0 0;">Pilih jenis nota yang akan dicetak</p>
            </div>
            <div style="padding:1.25rem; display:flex; flex-direction:column; gap:0.75rem;">
                <button onclick="closeLX310Popup(); setTimeout(cetakLX310, 120)"
                        style="width:100%; padding:0.875rem 1rem; background:#1f2937; color:#fff; border:none; border-radius:0.625rem; font-size:0.875rem; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:0.625rem; text-align:left;"
                        onmouseover="this.style.background='#111827'" onmouseout="this.style.background='#1f2937'">
                    <svg style="width:1.1rem;height:1.1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m0 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8m6 0v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4"/></svg>
                    <span>Nota Lengkap (2-ply)<br><small style="font-weight:400;color:#9ca3af;">Harga, total, TTD supplier</small></span>
                </button>
                <button onclick="closeLX310Popup(); setTimeout(cetakLX310Gudang, 120)"
                        style="width:100%; padding:0.875rem 1rem; background:#fff; color:#1f2937; border:2px solid #d1d5db; border-radius:0.625rem; font-size:0.875rem; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:0.625rem; text-align:left;"
                        onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
                    <svg style="width:1.1rem;height:1.1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Nota Gudang<br><small style="font-weight:400;color:#6b7280;">Nama barang &amp; qty saja — untuk persiapan gudang</small></span>
                </button>
                <button onclick="closeLX310Popup()"
                        style="width:100%; padding:0.625rem; background:transparent; color:#6b7280; border:none; font-size:0.8125rem; cursor:pointer; border-radius:0.5rem;"
                        onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ═══ NOTA GUDANG LX-310 (nama barang + qty saja) ═══ --}}
    <div id="print-lx310-gudang" style="display:none">
        <table width="100%" bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" style="font-family:Tahoma,Arial,sans-serif; font-size:12px; color:#000000;">

            {{-- Header --}}
            <tr><td colspan="5" align="center" style="border-bottom:2px solid #000; padding-bottom:3px;">
                <font face="Tahoma" size="3" color="000000"><b>{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</b></font><br>
                @if(!empty($cfg['toko_alamat']))<font face="Tahoma" size="1" color="000000">{{ $cfg['toko_alamat'] }}@if(!empty($cfg['toko_kota'])), {{ $cfg['toko_kota'] }}@endif</font><br>@endif
                <font face="Tahoma" size="2" color="000000"><b>==  NOTA GUDANG — PEMBELIAN  ==</b></font>
            </td></tr>

            {{-- Info singkat --}}
            <tr>
                <td colspan="3" style="padding:2px 0;"><font face="Tahoma" size="1" color="000000">No: <b>{{ $purchase->invoice_number }}</b></font></td>
                <td colspan="2" align="right" style="padding:2px 0;"><font face="Tahoma" size="1" color="000000">{{ $purchase->purchase_date->format('d/m/Y') }}</font></td>
            </tr>
            <tr>
                <td colspan="5" style="border-bottom:1px solid #000; padding-bottom:2px;">
                    <font face="Tahoma" size="1" color="000000">Supplier: <b>{{ $purchase->supplier?->name ?? '-' }}</b></font>
                </td>
            </tr>

            {{-- Tabel barang --}}
            <tr>
                <td width="5%" align="center" style="border-bottom:2px solid #000; padding:2px 0;"><font face="Tahoma" size="1" color="000000"><b>No</b></font></td>
                <td style="border-bottom:2px solid #000; padding:2px 4px;"><font face="Tahoma" size="2" color="000000"><b>Nama Barang</b></font></td>
                <td width="12%" align="right" style="border-bottom:2px solid #000; padding:2px 0;"><font face="Tahoma" size="2" color="000000"><b>Qty</b></font></td>
                <td width="12%" align="center" style="border-bottom:2px solid #000; padding:2px 4px;"><font face="Tahoma" size="2" color="000000"><b>Sat</b></font></td>
                <td width="8%" align="center" style="border-bottom:2px solid #000; padding:2px 0;"><font face="Tahoma" size="1" color="000000"><b>[ ]</b></font></td>
            </tr>
            @foreach($purchase->items as $i => $item)
            <tr>
                <td align="center" style="border-bottom:1px dashed #aaa; padding:3px 0;"><font face="Tahoma" size="1" color="000000">{{ $i+1 }}</font></td>
                <td style="border-bottom:1px dashed #aaa; padding:3px 4px;"><font face="Tahoma" size="2" color="000000"><b>{{ $item->product->name }}</b></font></td>
                <td align="right" style="border-bottom:1px dashed #aaa; padding:3px 0;"><font face="Tahoma" size="2" color="000000"><b>{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }}</b></font></td>
                <td align="center" style="border-bottom:1px dashed #aaa; padding:3px 4px;"><font face="Tahoma" size="1" color="000000">{{ $item->unit->unit_name }}</font></td>
                <td align="center" style="border-bottom:1px dashed #aaa; padding:3px 0;"><font face="Tahoma" size="1" color="000000">[ ]</font></td>
            </tr>
            @endforeach

            {{-- Footer --}}
            <tr><td colspan="5" style="border-top:2px solid #000; padding-top:3px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td><font face="Tahoma" size="1" color="000000">Total: <b>{{ $purchase->items->count() }} item</b></font></td>
                        <td align="right"><font face="Tahoma" size="1" color="000000">Dicetak: {{ now()->format('d/m/Y H:i') }}</font></td>
                    </tr>
                </table>
            </td></tr>

            {{-- TTD gudang --}}
            <tr><td colspan="5" style="padding-top:6px;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="50%" align="center"><font face="Tahoma" size="1" color="000000">Disiapkan Oleh</font></td>
                        <td width="50%" align="center"><font face="Tahoma" size="1" color="000000">Diperiksa Oleh</font></td>
                    </tr>
                    <tr>
                        <td height="28" style="border-bottom:1px solid #000;">&nbsp;</td>
                        <td height="28" style="border-bottom:1px solid #000;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center"><font face="Tahoma" size="1" color="000000">( ................... )</font></td>
                        <td align="center"><font face="Tahoma" size="1" color="000000">( ................... )</font></td>
                    </tr>
                </table>
            </td></tr>

        </table>
    </div>

    @push('styles')
    <style>
    #print-struk, #print-lx310, #print-lx310-gudang { display: none; }

    @media print {
        #screen-content, #lx310-popup, nav, header, .no-print { display: none !important; }
        * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        /* Struk 80mm */
        body.mode-struk { background:#fff !important; margin:0 !important; padding:0 !important; }
        body.mode-struk * { background:transparent !important; background-color:transparent !important; box-shadow:none !important; }
        body.mode-struk #print-struk { display:block !important; margin:0 !important; padding:0 !important; }
        body.mode-struk #print-struk > div { background:#fff !important; max-width:72mm !important; overflow:hidden !important; }
        body.mode-struk #print-struk * { color:#000 !important; }
        body.mode-struk #print-struk table { table-layout:fixed !important; width:100% !important; }
        body.mode-struk #print-struk td, body.mode-struk #print-struk th { overflow:hidden !important; word-break:break-word !important; white-space:normal !important; }

        /* LX-310 nota lengkap */
        body.mode-lx310 { background:#fff !important; margin:0 !important; padding:0 !important; }
        body.mode-lx310 * { background:transparent !important; background-color:transparent !important; box-shadow:none !important; }
        body.mode-lx310 #print-lx310 { display:block !important; background:#fff !important; }
        body.mode-lx310 #print-lx310 * { color:#000 !important; text-shadow:none !important; }

        /* LX-310 nota gudang */
        body.mode-lx310-gudang { background:#fff !important; margin:0 !important; padding:0 !important; }
        body.mode-lx310-gudang * { background:transparent !important; background-color:transparent !important; box-shadow:none !important; }
        body.mode-lx310-gudang #print-lx310-gudang { display:block !important; background:#fff !important; }
        body.mode-lx310-gudang #print-lx310-gudang * { color:#000 !important; text-shadow:none !important; }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    function _setPrintMode(mode, pageSize) {
        let ps = document.getElementById('ps-override');
        if (ps) ps.remove();
        ps = document.createElement('style');
        ps.id = 'ps-override';
        ps.textContent = '@media print { @page { ' + pageSize + ' } }';
        document.head.appendChild(ps);
        document.body.className = document.body.className
            .replace(/\bmode-\S+/g, '').trim();
        document.body.classList.add(mode);
        window.print();
        setTimeout(() => document.body.classList.remove(mode), 1500);
    }

    function cetakStruk()         { _setPrintMode('mode-struk',        'size:80mm auto; margin:3mm;'); }
    function cetakLX310()         { _setPrintMode('mode-lx310',        'size:A4 portrait; margin:10mm 15mm;'); }
    function cetakLX310Gudang()   { _setPrintMode('mode-lx310-gudang', 'size:A4 portrait; margin:10mm 15mm;'); }

    function openLX310Popup()  { document.getElementById('lx310-popup').style.display = 'flex'; }
    function closeLX310Popup() { document.getElementById('lx310-popup').style.display = 'none'; }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLX310Popup(); });

    @if(request('print') === 'lx310')
    window.addEventListener('load', () => setTimeout(cetakLX310, 500));
    @elseif(request('print'))
    window.addEventListener('load', () => setTimeout(cetakStruk, 500));
    @endif
    </script>
    @endpush

</x-app-layout>
