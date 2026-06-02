<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('absensi.admin') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800">Rekap Absensi Bulanan</h2>
            </div>
            <a href="{{ route('absensi.export', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV (Excel)
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filter --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                        <select name="bulan" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($bulanList as $num => $nama)
                            <option value="{{ $num }}" {{ $num == $bulan ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                        <select name="tahun" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @foreach($tahunList as $y)
                            <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                            Tampilkan
                        </button>
                    </div>
                    <div class="ml-auto">
                        <p class="text-sm font-semibold text-gray-700">{{ $bulanList[$bulan] }} {{ $tahun }}</p>
                    </div>
                </form>
            </div>

            {{-- Tabel Rekap --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Pegawai</th>
                            <th class="text-center px-4 py-3 font-semibold text-green-700">Hadir</th>
                            <th class="text-center px-4 py-3 font-semibold text-blue-700">Izin</th>
                            <th class="text-center px-4 py-3 font-semibold text-yellow-700">Sakit</th>
                            <th class="text-center px-4 py-3 font-semibold text-red-700">Alpha</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Total Dicatat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rekap as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $row['employee']->name }}</p>
                                @if($row['employee']->jabatan)
                                <p class="text-xs text-gray-400">{{ $row['employee']->jabatan }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-lg font-bold text-green-700">{{ $row['hadir'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['izin'] > 0)
                                <span class="text-base font-semibold text-blue-700">{{ $row['izin'] }}</span>
                                @else
                                <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['sakit'] > 0)
                                <span class="text-base font-semibold text-yellow-700">{{ $row['sakit'] }}</span>
                                @else
                                <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($row['alpha'] > 0)
                                <span class="text-base font-semibold text-red-700">{{ $row['alpha'] }}</span>
                                @else
                                <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-medium text-gray-600">{{ $row['total'] }} hari</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 text-sm">
                                Belum ada data absensi untuk periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($rekap->isNotEmpty())
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td class="px-5 py-3 font-semibold text-gray-700">Total</td>
                            <td class="px-4 py-3 text-center font-bold text-green-700">{{ $rekap->sum('hadir') }}</td>
                            <td class="px-4 py-3 text-center font-bold text-blue-700">{{ $rekap->sum('izin') }}</td>
                            <td class="px-4 py-3 text-center font-bold text-yellow-700">{{ $rekap->sum('sakit') }}</td>
                            <td class="px-4 py-3 text-center font-bold text-red-700">{{ $rekap->sum('alpha') }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-gray-600">{{ $rekap->sum('total') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
