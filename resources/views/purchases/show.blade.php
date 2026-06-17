<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" class="text-gray-400 hover:text-gray-600 transition">
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

            <div class="flex items-center gap-2">
                {{-- Tombol Cetak --}}
                <button onclick="cetakStruk()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 active:bg-gray-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Struk 80mm
                </button>
                <button onclick="cetakA4()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-900 active:bg-black transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Cetak A4
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

    <div class="py-6">
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
    @php $cfg = \App\Models\Setting::all(); @endphp
    <div id="print-struk" style="display:none">
        <div style="font-family:'Courier New',monospace; font-size:11px; width:72mm; margin:0 auto; line-height:1.45; color:#000;">

            <div style="text-align:center; margin-bottom:5px;">
                <div style="font-size:14px; font-weight:bold; letter-spacing:1px;">{{ strtoupper($cfg['toko_nama'] ?? config('app.name')) }}</div>
                @if(!empty($cfg['toko_tagline']))<div style="font-size:9px;">{{ $cfg['toko_tagline'] }}</div>@endif
                @if(!empty($cfg['nota_header']))<div style="font-size:9px;">{{ $cfg['nota_header'] }}</div>@endif
                @if(!empty($cfg['toko_alamat']))<div style="font-size:9px;">{{ $cfg['toko_alamat'] }}{{ !empty($cfg['toko_kota']) ? ', '.$cfg['toko_kota'] : '' }}</div>@endif
                @if(!empty($cfg['toko_telepon']))<div style="font-size:9px;">Telp: {{ $cfg['toko_telepon'] }}</div>@endif
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
                <div style="font-size:10px; font-weight:bold;">NOTA PEMBELIAN</div>
                <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>
            </div>

            <table style="width:100%; font-size:10px; border-collapse:collapse; margin-bottom:4px;">
                <tr><td style="width:38%">No. Faktur</td><td style="width:4%">:</td><td><b>{{ $purchase->invoice_number }}</b></td></tr>
                <tr><td>Tanggal</td><td>:</td><td>{{ $purchase->purchase_date->format('d/m/Y') }}</td></tr>
                <tr><td>Supplier</td><td>:</td><td>{{ $purchase->supplier?->name ?? '-' }}</td></tr>
                @if($purchase->buyer_name)<tr><td>Pembeli</td><td>:</td><td><b>{{ $purchase->buyer_name }}</b></td></tr>@endif
                <tr><td>Status</td><td>:</td><td>{{ $bl }}</td></tr>
                <tr><td>Dibuat</td><td>:</td><td>{{ $purchase->user?->name }}</td></tr>
            </table>
            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            @foreach($purchase->items as $item)
            <div style="font-size:10px; margin-bottom:4px;">
                <div style="font-weight:bold;">{{ $item->product->name }}</div>
                <div style="display:flex; justify-content:space-between; padding-left:4px;">
                    <span>{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }} {{ $item->unit->unit_name }} &times; {{ number_format($item->buy_price,0,',','.') }}</span>
                    <span style="font-weight:bold;">{{ number_format($item->subtotal,0,',','.') }}</span>
                </div>
            </div>
            @endforeach

            <div style="border-bottom:1px dashed #000; margin:4px 0;"></div>

            <table style="width:100%; font-size:10px; border-collapse:collapse;">
                <tr style="font-weight:bold; font-size:12px;">
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
            <div style="border-top:1px dashed #000; margin-top:5px; padding-top:4px; font-size:9px;">
                <b>Catatan:</b> {{ $purchase->notes }}
            </div>
            @endif

            <div style="border-top:1px dashed #000; margin-top:8px; padding-top:6px; font-size:9px;">
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
            <div style="text-align:center; font-size:9px; margin-top:8px; color:#555;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    {{-- ═══ CETAK A4 ═══ --}}
    <div id="print-a4" style="display:none">
        <div style="font-family:'Courier New', Courier, monospace; font-size:12px; color:#000; line-height: 1.2;">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:12px;">
                <div>
                    <div style="font-size:22px; font-weight:bold;">{{ $cfg['toko_nama'] ?? config('app.name') }}</div>
                    @if(!empty($cfg['toko_tagline']))<div style="font-size:11px; margin-top:2px;">{{ $cfg['toko_tagline'] }}</div>@endif
                    @if(!empty($cfg['nota_header']))<div style="font-size:11px; margin-top:2px;">{{ $cfg['nota_header'] }}</div>@endif
                    @if(!empty($cfg['toko_alamat']))<div style="font-size:11px; margin-top:4px;">{{ $cfg['toko_alamat'] }}{{ !empty($cfg['toko_kota']) ? ', '.$cfg['toko_kota'] : '' }}</div>@endif
                    @if(!empty($cfg['toko_telepon']))<div style="font-size:11px;">Telp: {{ $cfg['toko_telepon'] }}</div>@endif
                </div>
                <div style="text-align:right;">
                    <div style="font-size:18px; font-weight:bold; text-transform:uppercase; border:2px solid #000; padding:6px 14px; display:inline-block; letter-spacing:1px;">
                        @if($purchase->status === 'draft') PURCHASE ORDER @elseif($purchase->status === 'ordered') SURAT PESANAN @else BUKTI PEMBELIAN @endif
                    </div>
                    <div style="margin-top:8px; font-size:11px; line-height:1.6;">
                        <div><b>No. Dokumen</b> : {{ $purchase->invoice_number }}</div>
                        <div><b>Tanggal</b>     : {{ $purchase->purchase_date->format('d/m/Y') }}</div>
                        <div><b>Status</b>      : {{ strtoupper($bl) }}</div>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:20px; margin-bottom:14px;">
                <div style="flex:1; border:1px solid #000; padding:8px;">
                    <div style="font-size:10px; font-weight:bold; text-transform:uppercase; margin-bottom:4px;">Supplier / Dari</div>
                    <div style="font-weight:bold; font-size:12px;">{{ $purchase->supplier?->name ?? '-' }}</div>
                </div>
                <div style="flex:1; border:1px solid #000; padding:8px;">
                    <div style="font-size:10px; font-weight:bold; text-transform:uppercase; margin-bottom:4px;">Diterima Oleh</div>
                    <div style="font-weight:bold; font-size:12px;">{{ $cfg['toko_nama'] ?? config('app.name') }}</div>
                    <div style="font-size:11px; margin-top:4px;">Dibuat oleh: {{ $purchase->user?->name }}</div>
                </div>
                @if($purchase->buyer_name)
                <div style="flex:1; border:2px solid #000; padding:8px;">
                    <div style="font-size:10px; font-weight:bold; text-transform:uppercase; margin-bottom:4px;">Nama Pembeli</div>
                    <div style="font-weight:bold; font-size:13px;">{{ $purchase->buyer_name }}</div>
                </div>
                @endif
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:12px;">
                <thead>
                    <tr style="border-top:2px solid #000; border-bottom:2px solid #000;">
                        <th style="padding:7px 8px; text-align:center; width:4%;">No</th>
                        <th style="padding:7px 8px; text-align:left;">Nama Produk / SKU</th>
                        <th style="padding:7px 8px; text-align:center; width:10%;">Satuan</th>
                        <th style="padding:7px 8px; text-align:right; width:10%;">Qty</th>
                        <th style="padding:7px 8px; text-align:right; width:16%;">Harga Beli</th>
                        <th style="padding:7px 8px; text-align:right; width:16%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $i => $item)
                    <tr style="border-bottom:1px solid #000;">
                        <td style="padding:6px 8px; text-align:center;">{{ $i + 1 }}</td>
                        <td style="padding:6px 8px;">
                            <div style="font-weight:bold;">{{ $item->product->name }}</div>
                            <div style="font-size:10px;">SKU: {{ $item->product->sku }}</div>
                        </td>
                        <td style="padding:6px 8px; text-align:center;">{{ $item->unit->unit_name }}</td>
                        <td style="padding:6px 8px; text-align:right;">{{ rtrim(rtrim(number_format($item->qty,2,',','.'), '0'), ',') }}</td>
                        <td style="padding:6px 8px; text-align:right;">{{ number_format($item->buy_price,0,',','.') }}</td>
                        <td style="padding:6px 8px; text-align:right; font-weight:bold;">{{ number_format($item->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="border-top:2px solid #000; border-bottom:2px solid #000;">
                        <td colspan="5" style="padding:8px; text-align:right; font-weight:bold; font-size:13px;">TOTAL</td>
                        <td style="padding:8px; text-align:right; font-weight:bold; font-size:14px;">Rp {{ number_format($purchase->total_amount,0,',','.') }}</td>
                    </tr>
                    @if($purchase->paid_amount > 0)
                    <tr>
                        <td colspan="5" style="padding:6px 8px; text-align:right; font-size:11px;">Sudah Dibayar</td>
                        <td style="padding:6px 8px; text-align:right; font-size:11px; font-weight:bold;">{{ number_format($purchase->paid_amount,0,',','.') }}</td>
                    </tr>
                    @endif
                    @if($purchase->hutang > 0)
                    <tr>
                        <td colspan="5" style="padding:6px 8px; text-align:right; font-size:11px; font-weight:bold;">Sisa Hutang</td>
                        <td style="padding:6px 8px; text-align:right; font-size:11px; font-weight:bold;">{{ number_format($purchase->hutang,0,',','.') }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>

            @if($purchase->notes)
            <div style="border:1px solid #000; padding:8px; margin-bottom:12px; font-size:11px;">
                <b>Catatan:</b> {{ $purchase->notes }}
            </div>
            @endif

            <div style="display:flex; justify-content:space-between; margin-top:24px; gap:10px;">
                <div style="flex:1; text-align:center; font-size:11px;">
                    <div style="font-weight:bold;">Dibuat Oleh</div>
                    <div style="height:60px; border-bottom:1px solid #000; margin:8px 20px 4px;"></div>
                    <div>{{ $purchase->user?->name }}</div>
                    <div>{{ now()->format('d/m/Y') }}</div>
                </div>
                <div style="flex:1; text-align:center; font-size:11px;">
                    <div style="font-weight:bold;">Penerima Barang</div>
                    <div style="height:60px; border-bottom:1px solid #000; margin:8px 20px 4px;"></div>
                    <div>.............................</div>
                </div>
                <div style="flex:1; text-align:center; font-size:11px;">
                    <div style="font-weight:bold;">Supplier / Pengirim</div>
                    <div style="height:60px; border-bottom:1px solid #000; margin:8px 20px 4px;"></div>
                    <div>{{ $purchase->supplier?->name ?? '.......................' }}</div>
                </div>
            </div>

            <div style="border-top:1px solid #000; margin-top:16px; padding-top:6px; display:flex; justify-content:space-between; font-size:10px;">
                <span>{{ $cfg['toko_nama'] ?? config('app.name') }} — Dokumen dicetak otomatis oleh sistem</span>
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
        nav, header, .no-print, .py-6, .max-w-5xl { display: none !important; }

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

    @if(request('print') === 'a4')
    window.addEventListener('load', () => setTimeout(cetakA4, 500));
    @elseif(request('print'))
    window.addEventListener('load', () => setTimeout(cetakStruk, 500));
    @endif
    </script>
    @endpush

</x-app-layout>
