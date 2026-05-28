<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Supplier</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- Form Tambah Supplier --}}
                @can('pembelian.tambah')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 text-sm mb-4">Tambah Supplier</h3>
                    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="PT. Distributor ABC"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                          @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Kontak Person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                   placeholder="Nama sales / kontak"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                            <textarea name="address" rows="2" placeholder="Alamat lengkap..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                             focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                        </div>
                        <button type="submit"
                                class="w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Tambah Supplier
                        </button>
                    </form>
                </div>
                @endcan

                {{-- Daftar Supplier --}}
                <div class="md:col-span-2 space-y-3">
                    {{-- Search --}}
                    <form method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama atau telepon..."
                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            Cari
                        </button>
                        @if(request('search'))
                        <a href="{{ route('suppliers.index') }}"
                           class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                            Reset
                        </a>
                        @endif
                    </form>

                    @forelse($suppliers as $s)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4"
                         x-data="{ editing: false }">
                        <div x-show="!editing" class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-gray-800 text-sm">{{ $s->name }}</p>
                                    @if(!$s->is_active)
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
                                    @endif
                                </div>
                                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500">
                                    @if($s->phone)
                                    <span>📞 {{ $s->phone }}</span>
                                    @endif
                                    @if($s->contact_person)
                                    <span>👤 {{ $s->contact_person }}</span>
                                    @endif
                                    @if($s->address)
                                    <span>📍 {{ Str::limit($s->address, 50) }}</span>
                                    @endif
                                    <span class="text-gray-400">{{ $s->purchases_count }} pembelian</span>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                @can('pembelian.edit')
                                <button @click="editing = true"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                    Edit
                                </button>
                                @endcan
                                @can('pembelian.hapus')
                                <form method="POST" action="{{ route('suppliers.destroy', $s) }}"
                                      onsubmit="return confirm('Hapus supplier {{ $s->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                                        Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>

                        {{-- Inline edit form --}}
                        <div x-show="editing" x-cloak>
                            <form method="POST" action="{{ route('suppliers.update', $s) }}" class="space-y-3">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="col-span-2">
                                        <label class="text-xs font-medium text-gray-600">Nama *</label>
                                        <input type="text" name="name" value="{{ $s->name }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Telepon</label>
                                        <input type="text" name="phone" value="{{ $s->phone }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Kontak Person</label>
                                        <input type="text" name="contact_person" value="{{ $s->contact_person }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-xs font-medium text-gray-600">Alamat</label>
                                        <textarea name="address" rows="2"
                                                  class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">{{ $s->address }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" id="active_{{ $s->id }}"
                                               {{ $s->is_active ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600">
                                        <label for="active_{{ $s->id }}" class="text-sm text-gray-600">Aktif</label>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                        Simpan
                                    </button>
                                    <button type="button" @click="editing = false"
                                            class="px-4 py-1.5 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400 text-sm">
                        Belum ada supplier. Tambahkan dari form di kiri.
                    </div>
                    @endforelse

                    @if($suppliers->hasPages())
                    <div>{{ $suppliers->links() }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
