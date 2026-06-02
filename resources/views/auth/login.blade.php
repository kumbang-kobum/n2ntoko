<x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-700">{{ $errors->first() }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
            <div class="relative">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="w-4.5 h-4.5 w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="nama@email.com"
                       class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm transition
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white hover:border-gray-400' }}">
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <div class="relative" x-data="{ show: false }">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input id="password"
                       :type="show ? 'text' : 'password'"
                       name="password"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full pl-10 pr-11 py-3 border rounded-xl text-sm transition
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white hover:border-gray-400' }}">
                <button type="button" @click="show = !show"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg x-show="!show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Remember + Lupa Password --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input type="checkbox" name="remember" id="remember_me"
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                <span class="text-sm text-gray-600">Ingat saya</span>
            </label>

            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-sm text-blue-600 hover:text-blue-700 font-medium transition">
                Lupa password?
            </a>
            @endif
        </div>

        {{-- Tombol Login --}}
        <button type="submit"
                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                       text-white font-semibold text-sm rounded-xl transition
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                       shadow-sm shadow-blue-200">
            Masuk
        </button>

    </form>

    {{-- Info toko --}}
    @php $alamat = \App\Models\Setting::get('toko_alamat'); $tel = \App\Models\Setting::get('toko_telepon'); @endphp
    @if($alamat || $tel)
    <div class="mt-8 pt-6 border-t border-gray-200 text-center">
        @if($alamat)<p class="text-xs text-gray-400">{{ $alamat }}</p>@endif
        @if($tel)<p class="text-xs text-gray-400 mt-0.5">{{ $tel }}</p>@endif
    </div>
    @endif

</x-guest-layout>
