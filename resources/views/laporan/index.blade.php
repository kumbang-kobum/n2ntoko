<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Laporan Laba Rugi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Filter Periode --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4"
                 x-data="{ showKustom: '{{ $periode }}' === 'kustom' }">
                <form method="GET" action="{{ route('laporan.index') }}" class="flex flex-wrap items-end gap-3">

                    {{-- Tombol cepat --}}
                    <div class="flex flex-wrap gap-1.5">
                        @foreach([
                            'hari_ini'   => 'Hari Ini',
                            'minggu_ini' => 'Minggu Ini',
                            'bulan_ini'  => 'Bulan Ini',
                            'bulan_lalu' => 'Bulan Lalu',
                            'tahun_ini'  => 'Tahun Ini',
                            'kustom'     => 'Kustom',
                        ] as $val => $label)
                        <button type="submit" name="periode" value="{{ $val }}"
                            @if($val === 'kustom') @click="showKustom = true" @else @click="showKustom = false" @endif
                            class="px-3 py-1.5 text-sm rounded-lg border transition
                                {{ $periode === $val
                                    ? 'bg-blue-600 text-white border-blue-600'
                                    : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>

                    {{-- Rentang kustom --}}
                    <div x-show="showKustom" class="flex items-end gap-2 flex-wrap" style="display:none">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Dari</label>
                            <input type="date" name="dari" value="{{ request('dari', $from->toDateString()) }}"
                                class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Sampai</label>
                            <input type="date" name="sampai" value="{{ request('sampai', $to->toDateString()) }}"
                                class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none">
                        </div>
                        <input type="hidden" name="periode" value="kustom">
                        <button type="submit"
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            Tampilkan
                        </button>
                    </div>
                </form>

                <p class="text-xs text-gray-400 mt-2">
                    Periode:
                    <span class="font-medium text-gray-600">
                        {{ $from->translatedFormat('d F Y') }}
                        @if($from->toDateString() !== $to->toDateString())
                            — {{ $to->translatedFormat('d F Y') }}
                        @endif
                    </span>
                </p>
            </div>

            {{-- Kartu ringkasan --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Total Penjualan</p>
                    <p class="text-xl font-bold text-gray-800">
                        Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ number_format($jumlahTransaksi) }} transaksi</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">HPP (Harga Pokok)</p>
                    <p class="text-xl font-bold text-red-600">
                        Rp {{ number_format($totalHpp, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Akumulasi HPP terjual</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm
                    {{ $labaKotor >= 0 ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }}">
                    <p class="text-xs text-gray-400 mb-1">Laba Kotor</p>
                    <p class="text-xl font-bold {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($labaKotor, 0, ',', '.') }}
                    </p>
                    @if($totalPenjualan > 0)
                    <p class="text-xs {{ $labaKotor >= 0 ? 'text-green-500' : 'text-red-400' }} mt-1">
                        {{ number_format(($labaKotor / $totalPenjualan) * 100, 1) }}% margin
                    </p>
                    @else
                    <p class="text-xs text-gray-400 mt-1">—</p>
                    @endif
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Pembelian Diterima</p>
                    <p class="text-xl font-bold text-gray-700">
                        Rp {{ number_format($totalPembelian, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Stok masuk periode ini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Tabel harian --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-700 text-sm">Penjualan Harian</h3>
                        <span class="text-xs text-gray-400">{{ $harian->count() }} hari aktif</span>
                    </div>

                    @if($harian->isEmpty())
                    <div class="py-12 text-center text-gray-400 text-sm">Tidak ada transaksi di periode ini.</div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Trx</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Penjualan</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Laba</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Margin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($harian as $row)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ \Carbon\Carbon::parse($row->tgl)->translatedFormat('D, d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-500">{{ $row->trx }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                                        Rp {{ number_format($row->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium {{ $row->laba >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                        Rp {{ number_format($row->laba, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-gray-500">
                                        @if($row->total > 0)
                                            {{ number_format(($row->laba / $row->total) * 100, 1) }}%
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-semibold">
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">Total</td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ $harian->sum('trx') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-800">
                                        Rp {{ number_format($harian->sum('total'), 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right {{ $harian->sum('laba') >= 0 ? 'text-green-700' : 'text-red-600' }}">
                                        Rp {{ number_format($harian->sum('laba'), 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-500 text-xs">
                                        @if($harian->sum('total') > 0)
                                            {{ number_format(($harian->sum('laba') / $harian->sum('total')) * 100, 1) }}%
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Top produk --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-700 text-sm">Top 10 Produk</h3>
                    </div>

                    @if($topProduk->isEmpty())
                    <div class="py-12 text-center text-gray-400 text-sm">Belum ada data.</div>
                    @else
                    <div class="divide-y divide-gray-50">
                        @foreach($topProduk as $i => $item)
                        <div class="px-4 py-3 flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5
                                {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' :
                                   ($i === 1 ? 'bg-gray-100 text-gray-600' :
                                   ($i === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-400')) }}">
                                {{ $i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-800 truncate">
                                    {{ $item->product->name ?? '—' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    Rp {{ number_format($item->total_jual, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-sm font-semibold {{ $item->total_laba >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                    Rp {{ number_format($item->total_laba, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-400">laba</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Ringkasan L/R --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-700 text-sm mb-4">Ringkasan Laba Rugi</h3>
                <div class="space-y-2 max-w-sm">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pendapatan Penjualan</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">(-) HPP / Harga Pokok Penjualan</span>
                        <span class="font-medium text-red-600">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between text-sm font-semibold">
                        <span class="text-gray-700">= Laba Kotor</span>
                        <span class="{{ $labaKotor >= 0 ? 'text-green-700' : 'text-red-600' }}">
                            Rp {{ number_format($labaKotor, 0, ',', '.') }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 pt-1">
                        * Pengeluaran operasional (gaji, sewa, dll) belum diperhitungkan.
                        Laba ini adalah laba kotor dari selisih harga jual dan HPP rata-rata.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
