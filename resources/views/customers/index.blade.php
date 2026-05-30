<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Pelanggan</h2>
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

                {{-- Form Tambah --}}
                @can('pelanggan.tambah')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 text-sm mb-4">Tambah Pelanggan</h3>
                    <form method="POST" action="{{ route('customers.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Nama pelanggan"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipe <span class="text-red-500">*</span></label>
                            <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="umum"     {{ old('type') === 'umum'      ? 'selected' : '' }}>Umum</option>
                                <option value="grosir"   {{ old('type') === 'grosir'    ? 'selected' : '' }}>Grosir</option>
                                <option value="langganan"{{ old('type') === 'langganan' ? 'selected' : '' }}>Langganan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">No. Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                            <textarea name="address" rows="2" placeholder="Alamat pelanggan..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                        </div>
                        <button type="submit"
                                class="w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Tambah Pelanggan
                        </button>
                    </form>
                </div>
                @endcan

                {{-- Daftar Pelanggan --}}
                <div class="{{ Auth::user()->can('pelanggan.tambah') ? 'md:col-span-2' : 'md:col-span-3' }} space-y-3">
                    {{-- Filter --}}
                    <form method="GET" class="flex gap-2 flex-wrap">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama atau telepon..."
                               class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tipe</option>
                            <option value="umum"      {{ request('type') === 'umum'      ? 'selected' : '' }}>Umum</option>
                            <option value="grosir"    {{ request('type') === 'grosir'    ? 'selected' : '' }}>Grosir</option>
                            <option value="langganan" {{ request('type') === 'langganan' ? 'selected' : '' }}>Langganan</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Cari</button>
                        @if(request()->hasAny(['search','type']))
                        <a href="{{ route('customers.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Reset</a>
                        @endif
                    </form>

                    @forelse($customers as $c)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4" x-data="{ editing: false }">
                        <div x-show="!editing" class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800 text-sm">{{ $c->name }}</span>
                                    @php
                                        $typeColor = ['umum'=>'gray','grosir'=>'purple','langganan'=>'blue'][$c->type] ?? 'gray';
                                        $typeLabel = ['umum'=>'Umum','grosir'=>'Grosir','langganan'=>'Langganan'][$c->type] ?? $c->type;
                                    @endphp
                                    <span class="text-xs bg-{{ $typeColor }}-100 text-{{ $typeColor }}-700 px-2 py-0.5 rounded-full">{{ $typeLabel }}</span>
                                    @if(!$c->is_active)
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
                                    @endif
                                </div>
                                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500">
                                    @if($c->phone)<span>📞 {{ $c->phone }}</span>@endif
                                    @if($c->address)<span>📍 {{ Str::limit($c->address, 50) }}</span>@endif
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                @can('pelanggan.edit')
                                <button @click="editing = true" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
                                @endcan
                                @can('pelanggan.hapus')
                                <form method="POST" action="{{ route('customers.destroy', $c) }}"
                                      onsubmit="return confirm('Hapus pelanggan {{ addslashes($c->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </div>

                        {{-- Edit Inline --}}
                        <div x-show="editing" x-cloak>
                            <form method="POST" action="{{ route('customers.update', $c) }}" class="space-y-3">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="col-span-2">
                                        <label class="text-xs font-medium text-gray-600">Nama *</label>
                                        <input type="text" name="name" value="{{ $c->name }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Tipe *</label>
                                        <select name="type" class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            <option value="umum"      {{ $c->type === 'umum'      ? 'selected' : '' }}>Umum</option>
                                            <option value="grosir"    {{ $c->type === 'grosir'    ? 'selected' : '' }}>Grosir</option>
                                            <option value="langganan" {{ $c->type === 'langganan' ? 'selected' : '' }}>Langganan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Telepon</label>
                                        <input type="text" name="phone" value="{{ $c->phone }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-xs font-medium text-gray-600">Alamat</label>
                                        <textarea name="address" rows="2"
                                                  class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">{{ $c->address }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" id="cust_active_{{ $c->id }}"
                                               {{ $c->is_active ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600">
                                        <label for="cust_active_{{ $c->id }}" class="text-sm text-gray-600">Aktif</label>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Simpan</button>
                                    <button type="button" @click="editing = false" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400 text-sm">
                        Belum ada pelanggan.
                    </div>
                    @endforelse

                    @if($customers->hasPages())
                    <div>{{ $customers->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
