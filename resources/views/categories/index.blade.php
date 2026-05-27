<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategori Produk</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- Form Tambah --}}
            @can('produk.tambah')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-semibold text-gray-700 mb-3 text-sm">Tambah Kategori Baru</h3>
                <form method="POST" action="{{ route('categories.store') }}" class="flex gap-3">
                    @csrf
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                        placeholder="Nama kategori (contoh: Rokok, Minuman, Snack)" />
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition whitespace-nowrap">
                        + Tambah
                    </button>
                </form>
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endcan

            {{-- Daftar Kategori --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @forelse($categories as $category)
                <div x-data="{ editing: false }" class="flex items-center gap-4 px-5 py-3.5 border-b border-gray-50 last:border-0 hover:bg-gray-50">
                    <div class="flex-1">
                        {{-- Normal View --}}
                        <div x-show="!editing" class="flex items-center gap-3">
                            <span class="font-medium text-gray-800 text-sm">{{ $category->name }}</span>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                                {{ $category->products_count }} produk
                            </span>
                        </div>
                        {{-- Edit Inline --}}
                        <form x-show="editing" method="POST" action="{{ route('categories.update', $category) }}" class="flex gap-2">
                            @csrf @method('PATCH')
                            <input type="text" name="name" value="{{ $category->name }}"
                                class="flex-1 border border-blue-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700">Simpan</button>
                            <button type="button" @click="editing = false" class="border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg text-xs hover:bg-gray-50">Batal</button>
                        </form>
                    </div>

                    <div x-show="!editing" class="flex items-center gap-1">
                        @can('produk.edit')
                        <button @click="editing = true"
                            class="text-gray-400 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @endcan
                        @can('produk.hapus')
                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                            onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-gray-400 text-sm">
                    Belum ada kategori. Tambahkan kategori pertama di atas.
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
