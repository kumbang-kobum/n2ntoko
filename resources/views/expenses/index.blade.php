<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Pengeluaran</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            {{-- Ringkasan --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pengeluaran Bulan Ini</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
                    </div>
                    @can('pengeluaran.tambah')
                    <button onclick="document.getElementById('formTambah').classList.toggle('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Catat Pengeluaran
                    </button>
                    @endcan
                </div>
            </div>

            {{-- Form Tambah --}}
            @can('pengeluaran.tambah')
            <div id="formTambah" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-700 text-sm mb-4">Catat Pengeluaran Baru</h3>
                <form method="POST" action="{{ route('expenses.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('expense_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                @foreach($categories as $val => $label)
                                <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="500"
                                   placeholder="0"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan <span class="text-red-500">*</span></label>
                            <input type="text" name="description" value="{{ old('description') }}"
                                   placeholder="Contoh: Bayar listrik bulan Mei"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">No. Bukti / Referensi</label>
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                   placeholder="Opsional"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">Simpan</button>
                        <button type="button" onclick="document.getElementById('formTambah').classList.add('hidden')"
                                class="px-5 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Batal</button>
                    </div>
                </form>
            </div>
            @endcan

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                        <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Dari</label>
                        <input type="date" name="dari" value="{{ request('dari') }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Filter</button>
                    @if(request()->hasAny(['category','dari','sampai']))
                    <a href="{{ route('expenses.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Reset</a>
                    @endif
                </form>
            </div>

            {{-- Tabel --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Tanggal</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Kategori</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Keterangan</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">No. Bukti</th>
                                <th class="text-right px-4 py-3 font-medium text-gray-600">Jumlah</th>
                                <th class="text-left px-4 py-3 font-medium text-gray-600">Dicatat oleh</th>
                                <th class="w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($expenses as $e)
                            <tr x-data="{ editing: false }" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600" x-show="!editing">
                                    {{ $e->expense_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3" x-show="!editing">
                                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
                                        {{ $categories[$e->category] ?? $e->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700" x-show="!editing">{{ $e->description }}</td>
                                <td class="px-4 py-3 text-gray-400 text-xs" x-show="!editing">{{ $e->reference ?? '—' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-red-600" x-show="!editing">
                                    Rp {{ number_format($e->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs" x-show="!editing">{{ $e->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3" x-show="!editing">
                                    <div class="flex items-center gap-2 justify-end">
                                        @can('pengeluaran.edit')
                                        <button @click="editing = true" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
                                        @endcan
                                        @can('pengeluaran.hapus')
                                        <form method="POST" action="{{ route('expenses.destroy', $e) }}"
                                              onsubmit="return confirm('Hapus pengeluaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>

                                {{-- Row Edit --}}
                                <td colspan="7" class="px-4 py-3" x-show="editing" x-cloak>
                                    <form method="POST" action="{{ route('expenses.update', $e) }}">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                                            <div>
                                                <label class="text-xs font-medium text-gray-600">Tanggal</label>
                                                <input type="date" name="expense_date" value="{{ $e->expense_date->format('Y-m-d') }}"
                                                       class="w-full mt-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-600">Kategori</label>
                                                <select name="category" class="w-full mt-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                                    @foreach($categories as $val => $label)
                                                    <option value="{{ $val }}" {{ $e->category === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="text-xs font-medium text-gray-600">Keterangan</label>
                                                <input type="text" name="description" value="{{ $e->description }}"
                                                       class="w-full mt-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-600">Jumlah</label>
                                                <input type="number" name="amount" value="{{ $e->amount }}" min="1" step="500"
                                                       class="w-full mt-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-600">No. Bukti</label>
                                                <input type="text" name="reference" value="{{ $e->reference }}"
                                                       class="w-full mt-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                        <div class="flex gap-2 mt-3">
                                            <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Simpan</button>
                                            <button type="button" @click="editing = false" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Batal</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400">Belum ada catatan pengeluaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($expenses->isNotEmpty())
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 font-semibold text-gray-700 text-right">Total</td>
                                <td class="px-4 py-3 text-right font-bold text-red-600">
                                    Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @if($expenses->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $expenses->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
