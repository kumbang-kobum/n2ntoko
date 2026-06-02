<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Data Pegawai</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- Form Tambah --}}
                @can('pegawai.tambah')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-gray-700 text-sm mb-4">Tambah Pegawai</h3>
                    <form method="POST" action="{{ route('employees.store') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jabatan</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Kasir, Gudang, Admin..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">No. HP</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Bergabung</label>
                            <input type="date" name="join_date" value="{{ old('join_date') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Alamat</label>
                            <textarea name="address" rows="2" placeholder="Alamat tinggal..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Link Akun Login</label>
                            <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">— Tanpa Akun —</option>
                                @foreach($availableUsers as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Hubungkan ke akun pengguna (opsional)</p>
                        </div>
                        <button type="submit"
                                class="w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Tambah Pegawai
                        </button>
                    </form>
                </div>
                @endcan

                {{-- Daftar Pegawai --}}
                <div class="{{ Auth::user()->can('pegawai.tambah') ? 'md:col-span-2' : 'md:col-span-3' }} space-y-3">
                    {{-- Filter --}}
                    <form method="GET" class="flex gap-2 flex-wrap">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama, jabatan..."
                               class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="aktif"    {{ request('status') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">Cari</button>
                        @if(request()->hasAny(['search','status']))
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition">Reset</a>
                        @endif
                    </form>

                    @forelse($employees as $e)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4" x-data="{ editing: false }">
                        <div x-show="!editing" class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($e->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-gray-800 text-sm">{{ $e->name }}</span>
                                        @if($e->jabatan)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ $e->jabatan }}</span>
                                        @endif
                                        @if(!$e->is_active)
                                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500">
                                        @if($e->phone)<span>📞 {{ $e->phone }}</span>@endif
                                        @if($e->join_date)<span>📅 Bergabung {{ $e->join_date->format('d/m/Y') }}</span>@endif
                                        @if($e->user)<span class="text-green-600">🔗 {{ $e->user->name }}</span>@endif
                                        @if($e->address)<span>📍 {{ Str::limit($e->address, 40) }}</span>@endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                @can('pegawai.edit')
                                <button @click="editing = true" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
                                @endcan
                                @can('pegawai.hapus')
                                <form method="POST" action="{{ route('employees.destroy', $e) }}"
                                      onsubmit="return confirm('Hapus pegawai {{ addslashes($e->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </div>

                        {{-- Edit Inline --}}
                        <div x-show="editing" x-cloak>
                            <form method="POST" action="{{ route('employees.update', $e) }}" class="space-y-3">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="col-span-2">
                                        <label class="text-xs font-medium text-gray-600">Nama *</label>
                                        <input type="text" name="name" value="{{ $e->name }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Jabatan</label>
                                        <input type="text" name="jabatan" value="{{ $e->jabatan }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">No. HP</label>
                                        <input type="text" name="phone" value="{{ $e->phone }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Tanggal Bergabung</label>
                                        <input type="date" name="join_date" value="{{ $e->join_date?->format('Y-m-d') }}"
                                               class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600">Link Akun</label>
                                        <select name="user_id" class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                            <option value="">— Tanpa Akun —</option>
                                            @if($e->user)
                                            <option value="{{ $e->user_id }}" selected>{{ $e->user->name }}</option>
                                            @endif
                                            @foreach($availableUsers as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="text-xs font-medium text-gray-600">Alamat</label>
                                        <textarea name="address" rows="2"
                                                  class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">{{ $e->address }}</textarea>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" id="emp_act_{{ $e->id }}"
                                               {{ $e->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                                        <label for="emp_act_{{ $e->id }}" class="text-sm text-gray-600">Aktif</label>
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
                    <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400 text-sm">
                        Belum ada data pegawai.
                    </div>
                    @endforelse

                    @if($employees->hasPages())
                    <div>{{ $employees->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
