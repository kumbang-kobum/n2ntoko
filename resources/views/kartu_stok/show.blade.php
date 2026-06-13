<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('kartu-stok.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kartu Stok</h2>
                    <p class="text-sm text-gray-400 font-mono">{{ $product->name }} — {{ $product->sku }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 no-print">
                <a href="{{ route('kartu-stok.cetak', array_filter(['product' => $product->id, 'dari' => $dari, 'sampai' => $sampai])) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700
                          hover:bg-gray-50 px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Form
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        @media print {
            .no-print { display: none !important; }
            nav, header, footer { display: none !important; }
            body { background: white !important; }
            .print-page { padding: 0 !important; }
            .print-card {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }
    </style>

    <div class="py-6 print-page">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- ── Print Header (hanya muncul saat cetak) ── --}}
            <div class="hidden print:block mb-4">
                <div class="text-center mb-3">
                    <div class="text-base font-bold">{{ $settings['toko_nama'] ?? 'N2N Toko' }}</div>
                    @if(!empty($settings['toko_alamat']))
                    <div class="text-xs text-gray-500">
                        {{ $settings['toko_alamat'] }}{{ !empty($settings['toko_kota']) ? ', '.$settings['toko_kota'] : '' }}
                    </div>
                    @endif
                </div>
                <div class="border-t-2 border-b border-gray-800 py-1 text-center">
                    <span class="font-bold text-sm tracking-wide uppercase">Kartu Stok Barang</span>
                </div>
            </div>

            {{-- ── Info Produk ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 print-card">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <div class="text-xs text-gray-400 mb-1">Nama Produk</div>
                        <div class="font-semibold text-gray-800 text-base">{{ $product->name }}</div>
                        <div class="text-xs font-mono text-gray-400 mt-0.5">{{ $product->sku }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Satuan Dasar</div>
                        <div class="font-semibold text-gray-800">{{ $product->baseUnit?->unit_name ?? '—' }}</div>
                        <div class="text-xs text-gray-400">{{ $product->category?->name ?? 'Tanpa kategori' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 mb-1">Stok Saat Ini</div>
                        @php
                            $stokColor = match($product->stock_status) {
                                'habis'   => 'text-red-600',
                                'menipis' => 'text-orange-500',
                                default   => 'text-green-600',
                            };
                        @endphp
                        <div class="font-bold text-xl {{ $stokColor }}">
                            {{ number_format($product->stock_qty, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-400">{{ $product->baseUnit?->unit_name }}</div>
                    </div>
                </div>
            </div>

            {{-- ── Filter Tanggal ── --}}
            <form method="GET" action="{{ route('kartu-stok.show', $product) }}"
                  class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 no-print print-card">
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                        <input type="date" name="dari" value="{{ $dari }}"
                               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                        <input type="date" name="sampai" value="{{ $sampai }}"
                               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                        Tampilkan
                    </button>
                    @if($dari || $sampai)
                    <a href="{{ route('kartu-stok.show', $product) }}"
                       class="text-sm text-gray-400 hover:text-gray-600 transition">
                        Reset
                    </a>
                    @endif
                    <div class="ml-auto text-xs text-gray-400 self-center">
                        @if($dari || $sampai)
                            {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') : '—' }}
                            s/d
                            {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') : 'sekarang' }}
                        @else
                            Semua periode
                        @endif
                        · {{ $ledgers->count() }} transaksi
                    </div>
                </div>
            </form>

            {{-- ── Kartu Stok ── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden print-card">

                {{-- Info periode saat cetak --}}
                <div class="hidden print:flex justify-between items-center px-4 py-2 text-xs text-gray-500 border-b">
                    <span>
                        Periode:
                        {{ $dari   ? \Carbon\Carbon::parse($dari)->translatedFormat('d M Y')   : 'Semua' }}
                        —
                        {{ $sampai ? \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') : 'Sekarang' }}
                    </span>
                    <span>Dicetak: {{ now()->translatedFormat('d M Y H:i') }}</span>
                </div>

                @if($ledgers->isEmpty())
                <div class="py-16 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Belum ada pergerakan stok pada periode ini
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase w-8">No</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Tipe</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-green-600 uppercase">Masuk</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-red-500 uppercase">Keluar</th>
                                <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">

                            {{-- Baris saldo awal --}}
                            @if($ledgers->isNotEmpty())
                            @php $saldoAwal = $ledgers->first()->stock_before; @endphp
                            <tr class="bg-gray-50/70">
                                <td class="px-3 py-2.5 text-center text-gray-300 text-xs">—</td>
                                <td class="px-4 py-2.5 text-xs text-gray-400">
                                    {{ $dari ? \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') : \Carbon\Carbon::parse($ledgers->first()->created_at)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-2.5 text-xs text-gray-500 italic">Saldo awal periode</td>
                                <td class="px-3 py-2.5"></td>
                                <td class="px-4 py-2.5 text-right"></td>
                                <td class="px-4 py-2.5 text-right"></td>
                                <td class="px-4 py-2.5 text-right font-semibold text-gray-700">
                                    {{ number_format($saldoAwal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif

                            @foreach($ledgers as $i => $row)
                            @php
                                $qty = (float) $row->qty_base;
                                $masuk  = '';
                                $keluar = '';

                                if ($row->movement_type === 'in') {
                                    $masuk = $qty;
                                } elseif ($row->movement_type === 'out') {
                                    $keluar = abs($qty);
                                } else {
                                    // adjustment
                                    if ($qty >= 0) $masuk  = $qty;
                                    else           $keluar  = abs($qty);
                                }

                                $refLabel = match($row->reference_type) {
                                    'purchase'    => 'Pembelian',
                                    'sale'        => 'Penjualan',
                                    'stok_opname' => 'Stok Opname',
                                    default       => ucfirst($row->reference_type),
                                };

                                $tipeBadge = match($row->movement_type) {
                                    'in'         => 'bg-green-100 text-green-700',
                                    'out'        => 'bg-red-100 text-red-600',
                                    'adjustment' => $qty >= 0
                                                    ? 'bg-blue-100 text-blue-600'
                                                    : 'bg-orange-100 text-orange-600',
                                    default      => 'bg-gray-100 text-gray-500',
                                };
                                $tipeLabel = match($row->movement_type) {
                                    'in'         => 'Masuk',
                                    'out'        => 'Keluar',
                                    'adjustment' => $qty >= 0 ? 'Koreksi +' : 'Koreksi −',
                                    default      => $row->movement_type,
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-3 py-3 text-center text-xs text-gray-400">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    <div>{{ \Carbon\Carbon::parse($row->created_at)->translatedFormat('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-700">
                                        {{ $refLabel }}
                                        @if($row->reference_id)
                                        <span class="text-xs text-gray-400 font-mono">#{{ $row->reference_id }}</span>
                                        @endif
                                    </div>
                                    @if($row->notes)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $row->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full {{ $tipeBadge }}">
                                        {{ $tipeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    @if($masuk !== '')
                                    <span class="text-green-600">+{{ number_format($masuk, 0, ',', '.') }}</span>
                                    @else
                                    <span class="text-gray-200">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    @if($keluar !== '')
                                    <span class="text-red-500">{{ number_format($keluar, 0, ',', '.') }}</span>
                                    @else
                                    <span class="text-gray-200">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    {{ number_format($row->stock_after, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        {{-- Baris saldo akhir --}}
                        @if($ledgers->isNotEmpty())
                        <tfoot>
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td colspan="6" class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">
                                    Saldo Akhir
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-800 text-base">
                                    {{ number_format($ledgers->last()->stock_after, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-400 ml-1">{{ $product->baseUnit?->unit_name }}</span>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @endif
            </div>

            {{-- ── Ringkasan ── --}}
            @if($ledgers->isNotEmpty())
            @php
                $totalMasuk  = $ledgers->where('movement_type', 'in')->sum(fn($r) => abs((float)$r->qty_base))
                             + $ledgers->where('movement_type', 'adjustment')->filter(fn($r) => (float)$r->qty_base > 0)->sum(fn($r) => (float)$r->qty_base);
                $totalKeluar = $ledgers->where('movement_type', 'out')->sum(fn($r) => abs((float)$r->qty_base))
                             + $ledgers->where('movement_type', 'adjustment')->filter(fn($r) => (float)$r->qty_base < 0)->sum(fn($r) => abs((float)$r->qty_base));
            @endphp
            <div class="grid grid-cols-3 gap-4 no-print">
                <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
                    <div class="text-xs text-green-600 font-medium mb-1">Total Masuk</div>
                    <div class="text-xl font-bold text-green-700">{{ number_format($totalMasuk, 0, ',', '.') }}</div>
                    <div class="text-xs text-green-500">{{ $product->baseUnit?->unit_name }}</div>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
                    <div class="text-xs text-red-500 font-medium mb-1">Total Keluar</div>
                    <div class="text-xl font-bold text-red-600">{{ number_format($totalKeluar, 0, ',', '.') }}</div>
                    <div class="text-xs text-red-400">{{ $product->baseUnit?->unit_name }}</div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                    <div class="text-xs text-blue-600 font-medium mb-1">Nilai Stok Saat Ini</div>
                    <div class="text-xl font-bold text-blue-700">
                        Rp {{ number_format($product->stock_qty * $product->avg_cost, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-blue-400">HPP Rp {{ number_format($product->avg_cost, 0, ',', '.') }}</div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
