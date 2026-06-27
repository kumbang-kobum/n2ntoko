<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Riwayat Aktivitas</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('activity-log.index') }}"
                      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pengguna</label>
                        <input type="text" name="user" value="{{ request('user') }}" placeholder="Cari pengguna..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Produk / Dokumen</label>
                        <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Modul</label>
                        <select name="model" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Modul</option>
                            @foreach($modelLabels as $class => $label)
                                <option value="{{ $class }}" @selected(request('model') === $class)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Aksi</label>
                        <select name="event" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Aksi</option>
                            <option value="created"     @selected(request('event') === 'created')>Tambah</option>
                            <option value="updated"     @selected(request('event') === 'updated')>Edit</option>
                            <option value="deleted"     @selected(request('event') === 'deleted')>Hapus</option>
                            <option value="stok_opname" @selected(request('event') === 'stok_opname')>Stok Opname</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Dari</label>
                        <input type="date" name="dari" value="{{ request('dari') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3 xl:col-span-6 flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Filter
                        </button>
                        <a href="{{ route('activity-log.index') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabel Log --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ $logs->total() }} entri ditemukan</p>
                    @can('activity-log.hapus')
                    <form method="POST"
                          action="{{ route('activity-log.destroyAll') }}?{{ http_build_query(request()->only(['model','event','dari','sampai'])) }}"
                          onsubmit="return confirm('Hapus semua log yang sesuai filter? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 text-xs font-medium rounded-lg hover:bg-red-100 border border-red-200 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Semua (filter aktif)
                        </button>
                    </form>
                    @endcan
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Waktu</th>
                                <th class="px-4 py-3 text-left">Pengguna</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                                <th class="px-4 py-3 text-left">Modul</th>
                                <th class="px-4 py-3 text-left">Nama / Dokumen</th>
                                <th class="px-4 py-3 text-left">Keterangan Perubahan</th>
                                @can('activity-log.hapus')
                                <th class="px-4 py-3"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                            @php
                                $causerName  = $log->properties['causer_name']
                                    ?? optional($log->causer)->name
                                    ?? 'Sistem';

                                $subjectName = $log->properties['subject_name']
                                    ?? optional($log->subject)->name
                                    ?? optional($log->subject)->invoice_number
                                    ?? null;

                                $modulLabel = $modelLabels[$log->subject_type] ?? class_basename($log->subject_type ?? '');

                                $isOpname = $log->log_name === 'stok_opname';

                                $eventColor = match(true) {
                                    $isOpname              => 'bg-blue-100 text-blue-700',
                                    $log->event === 'created' => 'bg-green-100 text-green-700',
                                    $log->event === 'updated' => 'bg-yellow-100 text-yellow-700',
                                    $log->event === 'deleted' => 'bg-red-100 text-red-700',
                                    default                => 'bg-gray-100 text-gray-700',
                                };
                                $eventLabel = match(true) {
                                    $isOpname              => 'Stok Opname',
                                    $log->event === 'created' => 'Tambah',
                                    $log->event === 'updated' => 'Edit',
                                    $log->event === 'deleted' => 'Hapus',
                                    default                => ucfirst($log->event),
                                };

                                $props = $log->properties;
                            @endphp
                            <tr class="hover:bg-gray-50">

                                {{-- Waktu --}}
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                                    <div class="font-medium text-gray-700">{{ $log->created_at->format('d/m/Y') }}</div>
                                    <div>{{ $log->created_at->format('H:i:s') }}</div>
                                </td>

                                {{-- Pengguna --}}
                                <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                    {{ $causerName }}
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $eventColor }}">
                                        {{ $eventLabel }}
                                    </span>
                                </td>

                                {{-- Modul --}}
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $modulLabel }}</td>

                                {{-- Nama / Dokumen --}}
                                <td class="px-4 py-3">
                                    @if($subjectName)
                                        <span class="font-medium text-gray-800">{{ $subjectName }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">ID #{{ $log->subject_id ?? '-' }}</span>
                                    @endif
                                </td>

                                {{-- Keterangan Perubahan --}}
                                <td class="px-4 py-3 max-w-sm">
                                    @if($isOpname)
                                        {{-- Stok Opname: tampilkan qty sebelum → sesudah --}}
                                        <div class="space-y-0.5 text-xs">
                                            <div>
                                                <span class="text-gray-500">Stok:</span>
                                                <span class="text-red-500 line-through">{{ number_format($props['qty_sebelum'] ?? 0, 0, ',', '.') }}</span>
                                                <span class="text-gray-400 mx-0.5">→</span>
                                                <span class="text-green-600 font-semibold">{{ number_format($props['qty_fisik'] ?? 0, 0, ',', '.') }}</span>
                                                @php $selisih = $props['selisih'] ?? 0; @endphp
                                                <span class="{{ $selisih >= 0 ? 'text-green-600' : 'text-red-500' }} font-medium">
                                                    ({{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }})
                                                </span>
                                            </div>
                                            @if(!empty($props['catatan']))
                                            <div class="text-gray-400 italic">{{ $props['catatan'] }}</div>
                                            @endif
                                        </div>
                                    @elseif($log->event === 'updated' && isset($props['old'], $props['attributes']))
                                        {{-- Edit: tampilkan field yang berubah --}}
                                        <div class="space-y-1">
                                            @foreach(array_keys((array) $props['attributes']) as $field)
                                                @if(array_key_exists($field, (array) $props['old']) && $props['old'][$field] != $props['attributes'][$field])
                                                <div class="text-xs">
                                                    <span class="font-medium text-gray-500">{{ $field }}:</span>
                                                    <span class="line-through text-red-400">{{ Str::limit((string)($props['old'][$field] ?? ''), 35) }}</span>
                                                    <span class="text-gray-300 mx-0.5">→</span>
                                                    <span class="text-green-600">{{ Str::limit((string)($props['attributes'][$field] ?? ''), 35) }}</span>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($log->event === 'created' && isset($props['attributes']))
                                        <span class="text-xs text-gray-400">
                                            {{ collect($props['attributes'])->keys()->take(5)->implode(', ') }}
                                            @if(count($props['attributes']) > 5)
                                                <span class="text-gray-300">+{{ count($props['attributes']) - 5 }} lainnya</span>
                                            @endif
                                        </span>
                                    @elseif($log->event === 'deleted')
                                        <span class="text-xs text-red-400">Data dihapus</span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                @can('activity-log.hapus')
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('activity-log.destroy', $log) }}"
                                          onsubmit="return confirm('Hapus entri log ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus"
                                                class="text-gray-300 hover:text-red-500 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ auth()->user()->can('activity-log.hapus') ? 7 : 6 }}"
                                    class="px-4 py-10 text-center text-gray-400 text-sm">
                                    Belum ada aktivitas yang tercatat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
