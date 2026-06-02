<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Absensi Harian</h2>
            <div class="flex gap-2">
                @can('absensi.rekap')
                <a href="{{ route('absensi.rekap') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/>
                    </svg>
                    Rekap Bulanan
                </a>
                @endcan
                <a href="{{ route('absensi.public') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Halaman Absen Publik
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- Filter tanggal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <label class="text-sm font-medium text-gray-600">Tanggal:</label>
                    <input type="date" name="tanggal" value="{{ $date }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">Tampilkan</button>
                    @if($date !== today()->toDateString())
                    <a href="{{ route('absensi.admin') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset ke hari ini</a>
                    @endif
                    <span class="ml-auto text-sm font-semibold text-gray-700">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </span>
                </form>
            </div>

            {{-- Tabel absensi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Pegawai</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Jam Masuk</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Jam Pulang</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Durasi</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Selfie</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-600">Input</th>
                            <th class="px-4 py-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($employees as $emp)
                        @php $a = $attendances->get($emp->id); @endphp
                        <tr x-data="{ open: false }" class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $emp->name }}</p>
                                @if($emp->jabatan)<p class="text-xs text-gray-400">{{ $emp->jabatan }}</p>@endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($a)
                                    @php $colors = ['hadir'=>'green','izin'=>'blue','sakit'=>'yellow','alpha'=>'red']; $c = $colors[$a->status]??'gray'; @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700">
                                        {{ $a->status_label }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-gray-700">
                                {{ $a?->check_in ? substr($a->check_in,0,5) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-gray-700">
                                {{ $a?->check_out ? substr($a->check_out,0,5) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                {{ $a?->durasi ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($a?->photo_in)
                                    <a href="{{ asset('storage/'.$a->photo_in) }}" target="_blank" title="Selfie masuk">
                                        <img src="{{ asset('storage/'.$a->photo_in) }}"
                                             class="w-9 h-9 rounded-lg object-cover border-2 border-green-200 hover:scale-110 transition">
                                    </a>
                                    @endif
                                    @if($a?->photo_out)
                                    <a href="{{ asset('storage/'.$a->photo_out) }}" target="_blank" title="Selfie pulang">
                                        <img src="{{ asset('storage/'.$a->photo_out) }}"
                                             class="w-9 h-9 rounded-lg object-cover border-2 border-orange-200 hover:scale-110 transition">
                                    </a>
                                    @endif
                                    @if(!$a?->photo_in && !$a?->photo_out)
                                    <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-400">
                                {{ $a?->input_by ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @can('absensi.tambah')
                                <button @click="open = !open"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400
                                               hover:text-blue-600 hover:bg-blue-50 transition mx-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endcan
                            </td>
                        </tr>

                        {{-- Form edit inline --}}
                        @can('absensi.tambah')
                        <tr x-show="open" x-cloak class="bg-blue-50/60">
                            <td colspan="7" class="px-5 py-4">
                                <form method="POST" action="{{ route('absensi.store') }}"
                                      class="flex flex-wrap items-end gap-3">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                    <input type="hidden" name="date" value="{{ $date }}">

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                            @foreach(['hadir'=>'Hadir','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha'] as $val => $lbl)
                                            <option value="{{ $val }}" {{ ($a?->status ?? 'hadir') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jam Masuk</label>
                                        <input type="time" name="check_in" value="{{ $a?->check_in ? substr($a->check_in,0,5) : '' }}"
                                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Jam Pulang</label>
                                        <input type="time" name="check_out" value="{{ $a?->check_out ? substr($a->check_out,0,5) : '' }}"
                                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1 min-w-[160px]">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
                                        <input type="text" name="notes" value="{{ $a?->notes }}" placeholder="Opsional..."
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition font-medium">
                                            Simpan
                                        </button>
                                        @if($a)
                                        <form method="POST" action="{{ route('absensi.destroy', $a) }}" onsubmit="return confirm('Hapus absensi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50 transition">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif
                                        <button type="button" @click="open = false" class="px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-lg hover:bg-gray-50 transition">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @endcan
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">
                                Belum ada pegawai aktif.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan hari ini --}}
            @if($employees->isNotEmpty())
            @php
                $total  = $employees->count();
                $hadir  = $attendances->where('status','hadir')->count();
                $izin   = $attendances->where('status','izin')->count();
                $sakit  = $attendances->where('status','sakit')->count();
                $alpha  = $attendances->where('status','alpha')->count();
                $belum  = $total - $attendances->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-green-700">{{ $hadir }}</p>
                    <p class="text-xs text-green-600 mt-1">Hadir</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-blue-700">{{ $izin }}</p>
                    <p class="text-xs text-blue-600 mt-1">Izin</p>
                </div>
                <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-700">{{ $sakit }}</p>
                    <p class="text-xs text-yellow-600 mt-1">Sakit</p>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-red-700">{{ $alpha }}</p>
                    <p class="text-xs text-red-600 mt-1">Alpha</p>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold text-gray-500">{{ $belum }}</p>
                    <p class="text-xs text-gray-400 mt-1">Belum absen</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
