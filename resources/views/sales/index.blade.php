<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Penjualan</h2>
            @can('penjualan.tambah')
            <a href="{{ route('sales.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Transaksi Baru
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Omzet Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        Rp {{ number_format($stats['omzet_hari_ini'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Omzet Bulan Ini</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">
                        Rp {{ number_format($stats['omzet_bulan_ini'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['transaksi_hari_ini'] }}</p>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs text-gray-500 mb-1">Cari No. Faktur</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="JL-..."
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Status</label>
                        <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="paid" @selected(request('status')==='paid')>Lunas</option>
                            <option value="cancelled" @selected(request('status')==='cancelled')>Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Dari</label>
                        <input type="date" name="dari" value="{{ request('dari') }}"
                               class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}"
                               class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Filter</button>
                    @if(request()->hasAny(['search','status','dari','sampai']))
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">No. Faktur</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Kasir</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Tipe</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Item</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Total</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Status</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($sales as $s)
                            <tr class="hover:bg-gray-50 transition {{ $s->status === 'cancelled' ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3 font-medium text-blue-600">
                                    <a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $s->sale_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $s->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                                 {{ $s->price_type === 'grosir' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ ucfirst($s->price_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $s->items_count }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-800">
                                    Rp {{ number_format($s->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($s->status === 'paid')
                                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">Lunas</span>
                                    @else
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 font-medium">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('sales.show', $s) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Lihat</a>
                                        @if($s->status === 'paid')
                                            @can('penjualan.edit')
                                                <a href="{{ route('sales.edit', $s) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                            @endcan

                                            @can('penjualan.batal')
                                                <form method="POST" action="{{ route('sales.destroy', $s) }}"
                                                      onsubmit="return confirm('Batalkan penjualan ini? Stok akan dikembalikan.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Batal</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-gray-400">Belum ada data penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($sales->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $sales->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
