<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pengguna</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                            placeholder="Masukkan nama lengkap" autofocus />
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                            placeholder="contoh@email.com" />
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror"
                                placeholder="Minimal 6 karakter" />
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ulangi kata sandi" />
                    </div>

                    <hr class="border-gray-100">

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role / Jabatan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($roles as $role)
                            @php
                                $colors = [
                                    'admin'   => 'border-red-200 bg-red-50 text-red-700 peer-checked:border-red-500 peer-checked:bg-red-100',
                                    'manajer' => 'border-purple-200 bg-purple-50 text-purple-700 peer-checked:border-purple-500 peer-checked:bg-purple-100',
                                    'kasir'   => 'border-green-200 bg-green-50 text-green-700 peer-checked:border-green-500 peer-checked:bg-green-100',
                                    'gudang'  => 'border-orange-200 bg-orange-50 text-orange-700 peer-checked:border-orange-500 peer-checked:bg-orange-100',
                                ];
                                $desc = [
                                    'admin'   => 'Akses penuh semua fitur',
                                    'manajer' => 'Lihat laporan & data',
                                    'kasir'   => 'Transaksi penjualan',
                                    'gudang'  => 'Pembelian & stok',
                                ];
                                $colorClass = $colors[$role->name] ?? 'border-gray-200 bg-gray-50 text-gray-700 peer-checked:border-gray-500';
                            @endphp
                            <label class="relative cursor-pointer">
                                <input type="radio" name="role" value="{{ $role->name }}"
                                    class="peer sr-only"
                                    {{ old('role') === $role->name ? 'checked' : '' }}>
                                <div class="border-2 rounded-xl p-3 transition {{ $colorClass }}">
                                    <div class="font-semibold capitalize text-sm">{{ $role->name }}</div>
                                    <div class="text-xs mt-0.5 opacity-75">{{ $desc[$role->name] ?? '' }}</div>
                                </div>
                                <div class="absolute top-2 right-2 hidden peer-checked:block">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                        <div>
                            <div class="text-sm font-medium text-gray-700">Status Aktif</div>
                            <div class="text-xs text-gray-400">Pengguna non-aktif tidak bisa login</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}"
                        class="border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition">
                        Simpan Pengguna
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
