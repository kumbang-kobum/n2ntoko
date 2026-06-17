<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('laporan.index') }}" class="text-gray-400 hover:text-gray-600 transition no-print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-xl font-semibold text-gray-800">Laporan Per Kasir</h2>
            </div>
            <div class="flex gap-2 no-print">
                <button onclick="cetakA4()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak A4
                </button>
            </div>
        </div>
    </x-slot>

    {{-- ══════════════════════════════════════════════════════════
         TAMPILAN LAYAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="screen-only py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggal }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Kasir</label>
                        <select name="kasir_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kasir</option>
                            @foreach($kasirList as $k)
                            <option value="{{ $k->id }}" {{ $kasirId == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Tampilkan</button>
                    @if($kasirId)
                    <a href="{{ route('laporan.shift', ['tanggal' => $tanggal]) }}"
                       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                        Semua Kasir
                    </a>
                    @endif
                </form>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Total Penjualan</p>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $jumlahTransaksi }} transaksi</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">PPN Terpungut</p>
                    <p class="text-xl font-bold text-yellow-600">Rp {{ number_format($totalPPN, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">Subtotal: Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl p-4 border shadow-sm {{ $labaKotor >= 0 ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                    <p class="text-xs text-gray-400 mb-1">Laba Kotor</p>
                    <p class="text-xl font-bold {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($labaKotor, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">HPP: Rp {{ number_format($totalHpp, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl p-4 border shadow-sm {{ $labaBersih >= 0 ? 'bg-blue-50 border-blue-100' : 'bg-red-50 border-red-100' }}">
                    <p class="text-xs text-gray-400 mb-1">Laba Bersih</p>
                    <p class="text-xl font-bold {{ $labaBersih >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Rekapitulasi Metode Bayar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-700 text-sm mb-3">Rekapitulasi Metode Pembayaran</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @php $payLabels = ['cash'=>'Tunai','debit'=>'Debit/Transfer','qris'=>'QRIS']; @endphp
                    @foreach(['cash','debit','qris'] as $pm)
                    @php $row = $byPaymentTotal[$pm] ?? null; @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $payLabels[$pm] }}</p>
                            <p class="text-xs text-gray-400">{{ $row['trx'] ?? 0 }} transaksi</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800 text-sm">Rp {{ number_format($row['total'] ?? 0, 0, ',', '.') }}</p>
                            @if(($row['ppn'] ?? 0) > 0)
                            <p class="text-xs text-yellow-600">PPN: Rp {{ number_format($row['ppn'], 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Per Kasir --}}
            @foreach($byKasir as $kData)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($kData['user']->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $kData['user']->name }}</p>
                            <p class="text-xs text-gray-400">{{ $kData['trx'] }} transaksi</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-800">Rp {{ number_format($kData['total'], 0, ',', '.') }}</p>
                        @if($kData['ppn'] > 0)
                        <p class="text-xs text-yellow-600">PPN: Rp {{ number_format($kData['ppn'], 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Breakdown metode bayar kasir ini --}}
                <div class="px-5 py-3 border-b border-gray-50 flex flex-wrap gap-4">
                    @foreach(['cash','debit','qris'] as $pm)
                    @php $pd = $kData['by_payment'][$pm] ?? null; @endphp
                    @if($pd && $pd['trx'] > 0)
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $payLabels[$pm] }}</span>
                        <span class="font-medium">Rp {{ number_format($pd['total'], 0, ',', '.') }}</span>
                        <span class="text-gray-400 text-xs">({{ $pd['trx'] }} trx)</span>
                    </div>
                    @endif
                    @endforeach
                </div>

                {{-- Daftar transaksi kasir ini --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">Waktu</th>
                                <th class="text-left px-4 py-2 text-xs font-medium text-gray-500">No. Faktur</th>
                                <th class="text-center px-4 py-2 text-xs font-medium text-gray-500">Tipe</th>
                                <th class="text-center px-4 py-2 text-xs font-medium text-gray-500">Bayar</th>
                                <th class="text-right px-4 py-2 text-xs font-medium text-gray-500">Subtotal</th>
                                <th class="text-right px-4 py-2 text-xs font-medium text-gray-500">PPN</th>
                                <th class="text-right px-4 py-2 text-xs font-medium text-gray-500">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($kData['sales'] as $s)
                            @php
                                $pmColor = ['cash'=>'green','debit'=>'blue','qris'=>'purple'][$s->payment_method] ?? 'gray';
                                $pmLabel = ['cash'=>'Tunai','debit'=>'Debit','qris'=>'QRIS'][$s->payment_method] ?? '-';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $s->created_at->format('H:i') }}</td>
                                <td class="px-4 py-2.5 font-medium text-blue-600">
                                    <a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">{{ ucfirst($s->price_type) }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="text-xs bg-{{ $pmColor }}-100 text-{{ $pmColor }}-700 px-1.5 py-0.5 rounded-full font-medium">{{ $pmLabel }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-right text-gray-600">Rp {{ number_format($s->subtotal_before_tax, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-right text-yellow-600 text-xs">
                                    {{ $s->tax_amount > 0 ? 'Rp '.number_format($s->tax_amount, 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200 font-semibold text-xs">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right text-gray-600">Subtotal {{ $kData['user']->name }}</td>
                                <td class="px-4 py-2 text-right text-gray-700">Rp {{ number_format($kData['subtotal'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-yellow-600">Rp {{ number_format($kData['ppn'], 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right text-gray-900">Rp {{ number_format($kData['total'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endforeach

            @if($byKasir->isEmpty())
            <div class="bg-white rounded-xl p-12 text-center text-gray-400">Tidak ada transaksi pada tanggal ini.</div>
            @endif

            {{-- Laporan L/R + Pengeluaran --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 text-sm mb-4">Laporan Laba Rugi Harian</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Pendapatan Kotor</span><span class="font-medium">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">(-) PPN Terpungut</span><span class="font-medium text-yellow-600">Rp {{ number_format($totalPPN, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t border-dashed border-gray-200 pt-2"><span class="text-gray-600 font-medium">= Pendapatan Bersih</span><span class="font-semibold">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">(-) HPP</span><span class="font-medium text-red-500">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t border-dashed border-gray-200 pt-2"><span class="text-gray-600 font-medium">= Laba Kotor</span><span class="font-semibold {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">(-) Pengeluaran</span><span class="font-medium text-orange-500">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t-2 border-gray-300 pt-2 font-bold"><span class="text-gray-800">= Laba Bersih</span><span class="{{ $labaBersih >= 0 ? 'text-blue-700' : 'text-red-600' }} text-base">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span></div>
                    </div>
                </div>

                @if($expenses->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 text-sm mb-4">Pengeluaran Hari Ini</h3>
                    @php $catLabels = \App\Models\Expense::categoryLabels(); @endphp
                    <div class="space-y-2">
                        @foreach($expenses as $e)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $catLabels[$e->category] ?? $e->category }} — {{ $e->description }}</span>
                            <span class="font-medium text-orange-600">Rp {{ number_format($e->amount, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        <div class="border-t border-gray-200 pt-2 flex justify-between text-sm font-semibold">
                            <span>Total</span>
                            <span class="text-orange-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         PRINT A4 (Epson LX310 / Printer Biasa)
    ══════════════════════════════════════════════════════════ --}}
    <div class="print-a4" style="display:none">
        <div class="a4-header" style="font-family:'Courier New', Courier, monospace;">
            <div style="font-size:18px; font-weight:bold; text-align:center;">{{ config('app.name') }}</div>
            <div style="text-align:center; font-size:12px; margin-top:2px;">
                Laporan Penjualan Per Kasir
            </div>
            <div style="text-align:center; font-size:11px; margin-top:4px;">
                Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                @if($filterKasir) &nbsp;|&nbsp; Kasir: {{ $filterKasir->name }} @endif
            </div>
            <div style="border-top: 2px solid #000; margin-top:8px; padding-top:6px; display:flex; justify-content:space-between; font-size:12px; color:#000 !important; font-weight:bold;">
                <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
                <span>Total Transaksi: {{ $jumlahTransaksi }}</span>
            </div>
        </div>

        {{-- Rekap per kasir --}}
        @foreach($byKasir as $kData)
        <div style="margin-bottom:16px; page-break-inside:avoid; font-family:'Courier New', Courier, monospace; color:#000 !important;">
            <div style="padding:6px 0; font-weight:bold; font-size:13px; border-top:1px solid #000; border-bottom:1px solid #000;">
                KASIR: {{ strtoupper($kData['user']->name) }}
                &nbsp;·&nbsp; {{ $kData['trx'] }} Transaksi
                &nbsp;·&nbsp; Total: Rp {{ number_format($kData['total'], 0, ',', '.') }}
            </div>

            {{-- Breakdown metode bayar --}}
            <div style="display:flex; gap:16px; padding:4px 0; font-size:11px; border-bottom:1px dashed #000; font-weight:bold;">
                @foreach(['cash'=>'Tunai','debit'=>'Debit','qris'=>'QRIS'] as $pm => $lbl)
                @if(isset($kData['by_payment'][$pm]) && $kData['by_payment'][$pm]['trx'] > 0)
                <span>{{ $lbl }}: Rp {{ number_format($kData['by_payment'][$pm]['total'], 0, ',', '.') }} ({{ $kData['by_payment'][$pm]['trx'] }} trx)</span>
                @endif
                @endforeach
                @if($kData['ppn'] > 0)
                <span>PPN: Rp {{ number_format($kData['ppn'], 0, ',', '.') }}</span>
                @endif
            </div>

            <table style="width:100%; border-collapse:collapse; font-size:11px; margin-top:2px; color:#000 !important;">
                <thead>
                    <tr style="border-bottom:1px solid #000;">
                        <th style="text-align:left; padding:3px 4px;">Waktu</th>
                        <th style="text-align:left; padding:3px 4px;">No. Faktur</th>
                        <th style="text-align:center; padding:3px 4px;">Tipe</th>
                        <th style="text-align:center; padding:3px 4px;">Metode Bayar</th>
                        <th style="text-align:right; padding:3px 4px;">Subtotal</th>
                        <th style="text-align:right; padding:3px 4px;">PPN</th>
                        <th style="text-align:right; padding:3px 4px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kData['sales'] as $s)
                    <tr style="border-bottom:1px solid #000;">
                        <td style="padding:2px 4px;">{{ $s->created_at->format('H:i') }}</td>
                        <td style="padding:2px 4px;">{{ $s->invoice_number }}</td>
                        <td style="padding:2px 4px; text-align:center;">{{ ucfirst($s->price_type) }}</td>
                        <td style="padding:2px 4px; text-align:center;">{{ ['cash'=>'Tunai','debit'=>'Debit','qris'=>'QRIS'][$s->payment_method] ?? '-' }}</td>
                        <td style="padding:2px 4px; text-align:right;">{{ number_format($s->subtotal_before_tax, 0, ',', '.') }}</td>
                        <td style="padding:2px 4px; text-align:right;">{{ $s->tax_amount > 0 ? number_format($s->tax_amount, 0, ',', '.') : '-' }}</td>
                        <td style="padding:2px 4px; text-align:right; font-weight:bold;">{{ number_format($s->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr style="border-top:1px solid #000; font-weight:bold;">
                        <td colspan="4" style="padding:3px 4px; text-align:right;">SUBTOTAL {{ strtoupper($kData['user']->name) }}</td>
                        <td style="padding:3px 4px; text-align:right;">{{ number_format($kData['subtotal'], 0, ',', '.') }}</td>
                        <td style="padding:3px 4px; text-align:right;">{{ number_format($kData['ppn'], 0, ',', '.') }}</td>
                        <td style="padding:3px 4px; text-align:right;">{{ number_format($kData['total'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach

        {{-- Rekapitulasi Metode Bayar --}}
        <div style="margin-top:12px; border-top:2px solid #000; padding-top:8px; page-break-inside:avoid; font-family:'Courier New', Courier, monospace; color:#000 !important;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:6px;">REKAPITULASI METODE PEMBAYARAN</div>
            <table style="width:100%; border-collapse:collapse; font-size:11px; color:#000 !important;">
                <thead>
                    <tr style="border-bottom:1px solid #000;">
                        <th style="text-align:left; padding:2px 4px;">Metode</th>
                        <th style="text-align:center; padding:2px 4px;">Trx</th>
                        <th style="text-align:right; padding:2px 4px;">Subtotal</th>
                        <th style="text-align:right; padding:2px 4px;">PPN</th>
                        <th style="text-align:right; padding:2px 4px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['cash'=>'Tunai','debit'=>'Debit/Transfer','qris'=>'QRIS'] as $pm => $lbl)
                    @php $row = $byPaymentTotal[$pm] ?? null; @endphp
                    <tr style="border-bottom:1px solid #000;">
                        <td style="padding:2px 4px;">{{ $lbl }}</td>
                        <td style="padding:2px 4px; text-align:center;">{{ $row['trx'] ?? 0 }}</td>
                        <td style="padding:2px 4px; text-align:right;">{{ number_format($row['subtotal'] ?? 0, 0, ',', '.') }}</td>
                        <td style="padding:2px 4px; text-align:right;">{{ number_format($row['ppn'] ?? 0, 0, ',', '.') }}</td>
                        <td style="padding:2px 4px; text-align:right; font-weight:bold;">{{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr style="border-top:1px solid #000; font-weight:bold;">
                        <td style="padding:3px 4px;">TOTAL</td>
                        <td style="padding:3px 4px; text-align:center;">{{ $jumlahTransaksi }}</td>
                        <td style="padding:3px 4px; text-align:right;">{{ number_format($totalSubtotal, 0, ',', '.') }}</td>
                        <td style="padding:3px 4px; text-align:right;">{{ number_format($totalPPN, 0, ',', '.') }}</td>
                        <td style="padding:3px 4px; text-align:right;">{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Laporan L/R --}}
        <div style="margin-top:12px; border-top:2px solid #000; padding-top:8px; page-break-inside:avoid; font-family:'Courier New', Courier, monospace; color:#000 !important;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:6px;">LAPORAN LABA RUGI HARIAN</div>
            <table style="width:100%; border-collapse:collapse; font-size:11px; color:#000 !important;">
                <tr><td style="padding:2px 4px;">Pendapatan Kotor</td><td style="text-align:right; padding:2px 4px;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td></tr>
                <tr><td style="padding:2px 4px;">(-) PPN Terpungut</td><td style="text-align:right; padding:2px 4px;">Rp {{ number_format($totalPPN, 0, ',', '.') }}</td></tr>
                <tr style="border-top:1px dashed #000;"><td style="padding:2px 4px; font-weight:bold;">= Pendapatan Bersih</td><td style="text-align:right; padding:2px 4px; font-weight:bold;">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td></tr>
                <tr><td style="padding:2px 4px;">(-) HPP</td><td style="text-align:right; padding:2px 4px;">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td></tr>
                <tr style="border-top:1px dashed #000;"><td style="padding:2px 4px; font-weight:bold;">= Laba Kotor</td><td style="text-align:right; padding:2px 4px; font-weight:bold;">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td></tr>
                <tr><td style="padding:2px 4px;">(-) Pengeluaran</td><td style="text-align:right; padding:2px 4px;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr>
                <tr style="border-top:2px solid #000;"><td style="padding:3px 4px; font-weight:bold; font-size:12px;">= LABA BERSIH</td><td style="text-align:right; padding:3px 4px; font-weight:bold; font-size:12px;">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td></tr>
            </table>
        </div>

        @if($expenses->isNotEmpty())
        <div style="margin-top:12px; border-top:1px solid #000; padding-top:6px; page-break-inside:avoid; font-family:'Courier New', Courier, monospace; color:#000 !important;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:4px;">PENGELUARAN</div>
            @php $catLabels = \App\Models\Expense::categoryLabels(); @endphp
            @foreach($expenses as $e)
            <div style="display:flex; justify-content:space-between; font-size:11px; padding:1px 0;">
                <span>{{ $catLabels[$e->category] ?? $e->category }} — {{ $e->description }}</span>
                <span>Rp {{ number_format($e->amount, 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div style="display:flex; justify-content:space-between; font-size:11px; font-weight:bold; border-top:1px solid #000; padding-top:3px; margin-top:3px;">
                <span>TOTAL</span><span>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
            </div>
        </div>
        @endif

        <div style="text-align:center; font-size:11px; margin-top:20px; border-top:1px solid #000; padding-top:6px; font-family:'Courier New', Courier, monospace; color:#000 !important; font-weight:bold;">
            Dicetak oleh: {{ auth()->user()->name }} &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>

</x-app-layout>

@push('styles')
<style>
/* ── Print A4 (Epson LX310 / Printer Biasa) ── */
@media print {
    @page { size: A4 portrait; margin: 12mm 15mm; }

    .no-print, nav, header, .screen-only { display: none !important; }
    body > div > div { display: none !important; }

    .print-a4 {
        display: block !important;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        color: #000;
    }
    .a4-header { margin-bottom: 12px; }
}
</style>

<script>
function cetakA4() {
    window.print();
}
</script>
@endpush
