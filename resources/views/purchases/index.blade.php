<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Pembelian</h2>
            @can('pembelian.tambah')
            <a href="{{ route('purchases.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pembelian
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Total Pembelian Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        Rp {{ number_format($stats['total_bulan_ini'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Hutang Belum Lunas</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">
                        Rp {{ number_format($stats['hutang'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-sm text-gray-500">Draft Menunggu Konfirmasi</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['draft'] }}</p>
                </div>
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[180px]">
                        <label class="block text-xs text-gray-500 mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="No. faktur / supplier..."
                               class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Status</label>
                        <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="draft" @selected(request('status')==='draft')>Draft</option>
                            <option value="confirmed" @selected(request('status')==='confirmed')>Konfirmasi</option>
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
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search','status','dari','sampai']))
                    <a href="{{ route('purchases.index') }}"
                       class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Reset
                    </a>
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
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Supplier</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Item</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Total</th>
                                <th class="text-center px-4 py-3 font-medium text-gray-600">Status</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($purchases as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-blue-600">
                                    <a href="{{ route('purchases.show', $p) }}">{{ $p->invoice_number }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $p->purchase_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $p->supplier?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $p->items_count }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-800">
                                    Rp {{ number_format($p->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $colors = ['draft'=>'yellow','ordered'=>'blue','confirmed'=>'green','paid'=>'gray'];
                                        $labels = ['draft'=>'Draft','ordered'=>'Dipesan','confirmed'=>'Diterima','paid'=>'Lunas'];
                                        $c = $colors[$p->status] ?? 'gray';
                                        $l = $labels[$p->status] ?? $p->status;
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                 bg-{{ $c }}-100 text-{{ $c }}-700">
                                        {{ $l }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('purchases.show', $p) }}"
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">Lihat</a>
                                        <a href="{{ route('purchases.show', $p) }}?print=lx310"
                                           class="text-gray-500 hover:text-gray-700 text-xs font-medium" title="Cetak LX-310">
                                            <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>
                                        @if($p->status === 'draft')
                                            @can('pembelian.edit')
                                            <a href="{{ route('purchases.edit', $p) }}"
                                               class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Edit</a>
                                            <form method="POST" action="{{ route('purchases.order', $p) }}"
                                                  onsubmit="return confirm('Tandai sebagai Dipesan ke supplier?')">
                                                @csrf
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                    Pesan
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                        @if(in_array($p->status, ['draft','ordered']))
                                            @can('pembelian.konfirmasi')
                                            <form method="POST" action="{{ route('purchases.confirm', $p) }}"
                                                  onsubmit="return confirm('Tandai barang sudah diterima? Stok akan diperbarui.')">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">
                                                    Terima
                                                </button>
                                            </form>
                                            @endcan
                                            @can('pembelian.hapus')
                                            <form method="POST" action="{{ route('purchases.destroy', $p) }}"
                                                  onsubmit="return confirm('Hapus pembelian ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">
                                                    Hapus
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                    Belum ada data pembelian.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($purchases->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $purchases->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
