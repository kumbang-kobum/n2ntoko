<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Riwayat Aktivitas</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <form method="GET" action="{{ route('activity-log.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pengguna</label>
                        <input type="text" name="user" value="{{ request('user') }}" placeholder="Cari nama..."
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
                            <option value="created"  @selected(request('event') === 'created')>Tambah</option>
                            <option value="updated"  @selected(request('event') === 'updated')>Edit</option>
                            <option value="deleted"  @selected(request('event') === 'deleted')>Hapus</option>
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
                    <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
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
                    <form method="POST" action="{{ route('activity-log.destroyAll') }}?{{ http_build_query(request()->only(['model','event','dari','sampai'])) }}"
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
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-left">Pengguna</th>
                                <th class="px-4 py-3 text-left">Aksi</th>
                                <th class="px-4 py-3 text-left">Modul</th>
                                <th class="px-4 py-3 text-left">ID Data</th>
                                <th class="px-4 py-3 text-left">Perubahan</th>
                                @can('activity-log.hapus')
                                <th class="px-4 py-3"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                            @php
                                $causerName = $log->properties['causer_name']
                                    ?? optional($log->causer)->name
                                    ?? 'Sistem';
                                $modulLabel = $modelLabels[$log->subject_type] ?? class_basename($log->subject_type ?? '');
                                $eventColor = match($log->event) {
                                    'created' => 'bg-green-100 text-green-700',
                                    'updated' => 'bg-yellow-100 text-yellow-700',
                                    'deleted' => 'bg-red-100 text-red-700',
                                    default   => 'bg-gray-100 text-gray-700',
                                };
                                $eventLabel = match($log->event) {
                                    'created' => 'Tambah',
                                    'updated' => 'Edit',
                                    'deleted' => 'Hapus',
                                    default   => $log->event,
                                };
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $causerName }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $eventColor }}">
                                        {{ $eventLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $modulLabel }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $log->subject_id ?? '-' }}</td>
                                <td class="px-4 py-3 max-w-xs">
                                    @if($log->event === 'updated' && isset($log->properties['old'], $log->properties['attributes']))
                                        <div class="space-y-1">
                                            @foreach(array_keys((array) $log->properties['attributes']) as $field)
                                                @if(isset($log->properties['old'][$field]) && $log->properties['old'][$field] != $log->properties['attributes'][$field])
                                                <div class="text-xs">
                                                    <span class="font-medium text-gray-600">{{ $field }}:</span>
                                                    <span class="line-through text-red-400">{{ Str::limit((string)($log->properties['old'][$field] ?? ''), 30) }}</span>
                                                    <span class="text-gray-400">→</span>
                                                    <span class="text-green-600">{{ Str::limit((string)($log->properties['attributes'][$field] ?? ''), 30) }}</span>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($log->event === 'created' && isset($log->properties['attributes']))
                                        <span class="text-xs text-gray-400">
                                            {{ collect($log->properties['attributes'])->keys()->implode(', ') }}
                                        </span>
                                    @elseif($log->event === 'deleted')
                                        <span class="text-xs text-red-400">Data dihapus</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                @can('activity-log.hapus')
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('activity-log.destroy', $log) }}"
                                          onsubmit="return confirm('Hapus entri log ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-red-400 hover:text-red-600 transition"
                                                title="Hapus">
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
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">
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
