<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <title>{{ \App\Models\Setting::get('toko_nama', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" role="application" style="font-family:'Inter',sans-serif;">

<div class="min-h-screen flex">

    {{-- Panel Kiri — Branding (tersembunyi di mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 flex-col justify-between p-12 relative overflow-hidden">

        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full"></div>
            <div class="absolute top-1/3 -left-12 w-64 h-64 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-16 right-16 w-80 h-80 bg-white/5 rounded-full"></div>
        </div>

        {{-- Logo & Nama Toko --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-white font-bold text-lg">{{ \App\Models\Setting::get('toko_nama', config('app.name')) }}</span>
            </div>
        </div>

        {{-- Tagline Tengah --}}
        <div class="relative z-10">
            <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                Kelola Toko<br>Lebih Mudah &amp;<br>
                <span class="text-blue-200">Lebih Akurat</span>
            </h1>
            <p class="text-blue-100 text-base leading-relaxed max-w-sm">
                {{ \App\Models\Setting::get('toko_tagline', 'Sistem POS & Manajemen Stok untuk bisnis yang berkembang.') }}
            </p>

            {{-- Fitur singkat --}}
            <div class="mt-8 space-y-3">
                @foreach([
                    ['icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label'=>'Barcode Scanner'],
                    ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10', 'label'=>'Laporan Laba Rugi'],
                    ['icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'label'=>'Stok Multi-Satuan'],
                ] as $f)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-blue-100 text-sm">{{ $f['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer panel kiri --}}
        <div class="relative z-10">
            <p class="text-blue-200 text-xs">&copy; {{ date('Y') }} {{ \App\Models\Setting::get('toko_nama', config('app.name')) }}</p>
        </div>
    </div>

    {{-- Panel Kanan — Form Login --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
        <div class="w-full max-w-md">

            {{-- Tombol Kembali --}}
            <div class="mb-6">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition group">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>

            {{-- Logo mobile (hanya tampil di bawah lg) --}}
            <div class="flex items-center justify-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-900 text-xl">{{ \App\Models\Setting::get('toko_nama', config('app.name')) }}</span>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                <p class="text-gray-500 text-sm mt-1">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            {{ $slot }}

        </div>
    </div>

</div>

</body>
</html>
