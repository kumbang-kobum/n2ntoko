<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Manajemen Hak Akses</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            {{-- Info --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
                <strong>Catatan:</strong> Role <strong>Admin</strong> selalu mendapat semua permission dan tidak bisa diubah.
                Perubahan permission berlaku segera setelah disimpan.
            </div>

            {{-- Tambah Role Baru --}}
            @can('hakakses.edit')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700 text-sm">Role yang Ada</h3>
                    <button @click="open = !open"
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Role
                    </button>
                </div>

                <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-gray-100">
                    <form method="POST" action="{{ route('hakakses.storeRole') }}" class="flex flex-wrap gap-3 items-end">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Nama Role <span class="text-gray-400">(hanya huruf kecil & underscore, misal: supervisor)</span>
                            </label>
                            <input type="text" name="name" placeholder="nama_role"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Buat</button>
                        <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Batal</button>
                    </form>
                </div>

                {{-- Daftar role --}}
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($roles as $role)
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium
                        {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ ucfirst($role->name) }}
                        <span class="text-xs opacity-60">({{ $role->permissions->count() }} permission)</span>
                        @if(!in_array($role->name, ['admin','kasir','manajer','gudang']))
                        <form method="POST" action="{{ route('hakakses.destroyRole', $role) }}"
                              onsubmit="return confirm('Hapus role {{ $role->name }}?')" class="inline">
                            @csrf @method('DELETE')

                            <button type="submit" class="ml-1 text-red-400 hover:text-red-600">×</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endcan

            {{-- Matrix Permission per Role --}}
            @foreach($roles as $role)
            @if($role->name === 'admin') @continue @endif
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ ucfirst($role->name) }}</h3>
                            <p class="text-xs text-gray-400">{{ $role->permissions->count() }} permission aktif</p>
                        </div>
                    </div>
                </div>

                @can('hakakses.edit')
                <form method="POST" action="{{ route('hakakses.update', $role) }}">
                    @csrf @method('PUT')
                @endcan

                    <div class="p-5 space-y-4">
                        @foreach($permissions as $group => $groupPerms)
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                {{ ucfirst($group) }}
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                @foreach($groupPerms as $perm)
                                @php
                                    $active = $role->permissions->contains('name', $perm->name);
                                    $suffix = explode('.', $perm->name)[1] ?? $perm->name;
                                @endphp
                                <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition
                                    {{ $active ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50' }}
                                    hover:border-blue-300 hover:bg-blue-50">
                                    @can('hakakses.edit')
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                           {{ $active ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shrink-0">
                                    @else
                                    <span class="w-4 h-4 rounded border {{ $active ? 'bg-blue-600 border-blue-600' : 'border-gray-300' }} shrink-0 flex items-center justify-center">
                                        @if($active)<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>@endif
                                    </span>
                                    @endcan
                                    <span class="text-xs {{ $active ? 'text-blue-700 font-medium' : 'text-gray-500' }} capitalize">
                                        {{ str_replace(['lihat','tambah','edit','hapus','konfirmasi','batal','ekspor'],
                                           ['Lihat','Tambah','Edit','Hapus','Konfirmasi','Batal','Ekspor'], $suffix) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                @can('hakakses.edit')
                    <div class="px-5 pb-4">
                        <button type="submit"
                                class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Simpan Permission {{ ucfirst($role->name) }}
                        </button>
                    </div>
                </form>
                @endcan
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
