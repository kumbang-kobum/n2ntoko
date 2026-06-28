<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800">
                Hak Akses Khusus — {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- Info User --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-gray-800">{{ $user->name }}</div>
                        <div class="text-sm text-gray-400">{{ $user->email }}</div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                        @php
                            $colors = [
                                'admin'   => 'bg-red-100 text-red-700',
                                'manajer' => 'bg-purple-100 text-purple-700',
                                'kasir'   => 'bg-green-100 text-green-700',
                                'gudang'  => 'bg-orange-100 text-orange-700',
                            ];
                            $color = $colors[$role->name] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="text-xs font-medium px-3 py-1.5 rounded-full capitalize {{ $color }}">
                            Role: {{ $role->name }}
                        </span>
                        @endforeach
                        @if($directPermNames->count() > 0)
                        <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-blue-100 text-blue-700">
                            +{{ $directPermNames->count() }} permission khusus
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Penjelasan --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800 space-y-1">
                <p><strong>Cara kerja hak akses hybrid:</strong></p>
                <ul class="mt-1.5 space-y-1 list-none">
                    <li class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded border-2 border-purple-400 bg-purple-100 shrink-0"></span>
                        <span><strong>Dari Role</strong> — aktif karena role pengguna, tidak bisa diubah di sini (gunakan menu Hak Akses)</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded bg-blue-500 shrink-0"></span>
                        <span><strong>Permission Khusus</strong> — diberikan langsung ke pengguna ini, di luar role-nya</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded border-2 border-gray-300 bg-white shrink-0"></span>
                        <span><strong>Tidak aktif</strong> — centang untuk memberikan akses tambahan</span>
                    </li>
                </ul>
            </div>

            {{-- Grid Permission --}}
            @can('hakakses.edit')
            <form method="POST" action="{{ route('users.permissions.update', $user) }}">
                @csrf @method('PUT')
            @endcan

                @foreach($allPermissions as $group => $groupPerms)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide">{{ ucfirst($group) }}</h4>
                    </div>
                    <div class="p-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                        @foreach($groupPerms as $perm)
                        @php
                            $fromRole   = isset($rolePermNames[$perm->name]);
                            $isDirect   = isset($directPermNames[$perm->name]);
                            $suffix     = explode('.', $perm->name)[1] ?? $perm->name;
                            $labelMap   = [
                                'lihat'      => 'Lihat',
                                'tambah'     => 'Tambah',
                                'edit'       => 'Edit',
                                'hapus'      => 'Hapus',
                                'konfirmasi' => 'Konfirmasi',
                                'batal'      => 'Batal',
                                'ekspor'     => 'Ekspor',
                            ];
                            $label = $labelMap[$suffix] ?? ucfirst($suffix);
                        @endphp

                        @if($fromRole)
                        {{-- Permission dari Role: hanya tampilan, bukan input form --}}
                        <div class="flex items-center gap-2 p-2 rounded-lg border border-purple-200 bg-purple-50">
                            <span class="w-4 h-4 rounded border-2 border-purple-400 bg-purple-100 shrink-0 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <span class="text-xs text-purple-700 font-medium leading-tight flex-1">{{ $label }}</span>
                            <span class="text-[10px] text-purple-400 shrink-0">role</span>
                        </div>

                        @else
                        {{-- Permission langsung / kosong: form checkbox --}}
                        <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition
                            {{ $isDirect ? 'border-blue-300 bg-blue-50' : 'border-gray-200 bg-gray-50 hover:border-blue-200 hover:bg-blue-50' }}">
                            @can('hakakses.edit')
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                   {{ $isDirect ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shrink-0">
                            @else
                            <span class="w-4 h-4 rounded border {{ $isDirect ? 'bg-blue-600 border-blue-600' : 'border-gray-300 bg-white' }} shrink-0 flex items-center justify-center">
                                @if($isDirect)
                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                @endif
                            </span>
                            @endcan
                            <span class="text-xs leading-tight flex-1
                                {{ $isDirect ? 'text-blue-700 font-medium' : 'text-gray-500' }}">
                                {{ $label }}
                            </span>
                        </label>
                        @endif

                        @endforeach
                    </div>
                </div>
                @endforeach

            @can('hakakses.edit')
                <div class="flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Permission dari role tidak terpengaruh oleh form ini.
                        Hanya permission tambahan (centang biru) yang akan disimpan.
                    </p>
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition shrink-0">
                        Simpan Hak Akses Khusus
                    </button>
                </div>
            </form>
            @endcan

        </div>
    </div>
</x-app-layout>
