<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no,date=no,address=no,email=no">
    @php $tokoNama = \App\Models\Setting::get('toko_nama', config('app.name')); @endphp
    <title>{{ $tokoNama }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white" style="font-family:'Inter',sans-serif;">

{{-- ══════════════════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════════════════════ --}}
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100" x-data="{ open: false }">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between gap-4">

        {{-- Logo --}}
        <a href="#" class="flex items-center gap-2.5 shrink-0">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <span class="font-bold text-gray-900 text-sm">{{ $tokoNama }}</span>
        </a>

        {{-- Menu desktop --}}
        <div class="hidden sm:flex items-center gap-1">
            <a href="#tentang"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition font-medium">
                Tentang
            </a>
            <a href="{{ route('absensi.public') }}"
               class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition font-medium">
                Absensi
            </a>
        </div>

        {{-- Login button --}}
        <a href="{{ route('login') }}"
           class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 active:bg-blue-800 transition shrink-0">
            Login
        </a>
    </div>
</nav>

{{-- ══════════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════════ --}}
<section class="max-w-5xl mx-auto px-4 pt-20 pb-16 text-center">

    <span class="inline-block bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full mb-6 tracking-wide uppercase">
        Sistem POS &amp; Manajemen Stok
    </span>

    <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight mb-5">
        Kelola Toko Lebih Mudah<br>
        <span class="text-blue-600">&amp; Lebih Akurat</span>
    </h1>

    <p class="text-gray-500 text-base sm:text-lg max-w-xl mx-auto mb-10 leading-relaxed">
        {{ \App\Models\Setting::get('toko_tagline', 'Solusi pencatatan penjualan, pembelian, dan stok untuk toko Anda.') }}
    </p>

    <div class="flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                  text-white font-bold px-7 py-3.5 rounded-xl text-sm transition shadow-sm shadow-blue-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk ke Aplikasi
        </a>
        <a href="{{ route('absensi.public') }}"
           class="inline-flex items-center gap-2 border border-gray-200 hover:bg-gray-50 active:bg-gray-100
                  text-gray-700 font-semibold px-6 py-3.5 rounded-xl text-sm transition">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Absensi Pegawai
        </a>
    </div>
</section>

{{-- Garis pemisah tipis --}}
<div class="max-w-5xl mx-auto px-4">
    <hr class="border-gray-100">
</div>

{{-- ══════════════════════════════════════════════════════════
     FITUR SINGKAT
════════════════════════════════════════════════════════════ --}}
<section class="max-w-5xl mx-auto px-4 py-14">
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        @foreach([
            ['bg'=>'blue',  'icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'title'=>'Barcode Scanner', 'desc'=>'Scan produk langsung dari kamera atau scanner USB'],
            ['bg'=>'green', 'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'title'=>'Multi-Satuan', 'desc'=>'Beli per karton, jual per pcs — stok selalu akurat'],
            ['bg'=>'purple','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10', 'title'=>'Laporan Laba', 'desc'=>'HPP otomatis, profit tiap transaksi tercatat rapi'],
            ['bg'=>'orange','icon'=>'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z', 'title'=>'Harga Grosir & Eceran', 'desc'=>'Harga berbeda per tipe pelanggan, dipilih otomatis'],
            ['bg'=>'red',  'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'title'=>'Hak Akses Role', 'desc'=>'Admin, Kasir, Gudang, Manajer — akses sesuai tugas'],
            ['bg'=>'teal', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title'=>'Absensi Pegawai', 'desc'=>'Check-in/out tanpa login, rekap bulanan & export CSV'],
        ] as $f)
        <div class="flex items-start gap-3 bg-gray-50 rounded-2xl p-4 border border-gray-100">
            <div class="w-9 h-9 bg-{{ $f['bg'] }}-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4.5 h-4.5 w-[18px] h-[18px] text-{{ $f['bg'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">{{ $f['title'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     TENTANG
════════════════════════════════════════════════════════════ --}}
<section id="tentang" class="bg-gray-50 border-y border-gray-100">
    <div class="max-w-5xl mx-auto px-4 py-16 space-y-14">

        {{-- Heading --}}
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3">Tentang Aplikasi</h2>
            <p class="text-gray-500 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                {{ \App\Models\Setting::get('toko_nama', config('app.name')) }} adalah sistem informasi toko berbasis web
                yang dirancang untuk membantu pengelolaan penjualan, pembelian, stok, dan laporan keuangan secara terintegrasi.
            </p>
        </div>

        {{-- Cara Penggunaan --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-5 text-center">Cara Penggunaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach([
                    ['no'=>'1', 'title'=>'Login', 'desc'=>'Masuk menggunakan akun yang diberikan oleh administrator.'],
                    ['no'=>'2', 'title'=>'Input Produk', 'desc'=>'Daftarkan produk beserta satuan, harga, dan barcode di menu Produk.'],
                    ['no'=>'3', 'title'=>'Transaksi', 'desc'=>'Buat pembelian dari supplier atau proses penjualan lewat kasir.'],
                    ['no'=>'4', 'title'=>'Pantau Laporan', 'desc'=>'Cek laporan laba rugi, rekap shift, dan stok di menu Laporan.'],
                ] as $step)
                <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center shadow-sm">
                    <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm mx-auto mb-3">
                        {{ $step['no'] }}
                    </div>
                    <p class="font-semibold text-gray-800 text-sm mb-1.5">{{ $step['title'] }}</p>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Peran & Akses --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-5 text-center">Peran &amp; Hak Akses</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach([
                    ['role'=>'Admin',   'color'=>'blue',  'akses'=>['Semua fitur & pengaturan','Manajemen pengguna & role','Hak akses penuh']],
                    ['role'=>'Manajer', 'color'=>'purple','akses'=>['Lihat semua laporan','Rekap absensi & ekspor','Pantau stok & hutang']],
                    ['role'=>'Kasir',   'color'=>'green', 'akses'=>['Input transaksi penjualan','Cetak struk & nota','Lihat data produk']],
                    ['role'=>'Gudang',  'color'=>'orange','akses'=>['Input pembelian','Konfirmasi penerimaan barang','Update stok opname']],
                ] as $r)
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                    <span class="inline-block bg-{{ $r['color'] }}-100 text-{{ $r['color'] }}-700 text-xs font-bold px-2.5 py-1 rounded-full mb-3">
                        {{ $r['role'] }}
                    </span>
                    <ul class="space-y-1.5">
                        @foreach($r['akses'] as $a)
                        <li class="flex items-start gap-2 text-xs text-gray-600">
                            <svg class="w-3.5 h-3.5 text-{{ $r['color'] }}-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $a }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Info Kontak --}}
        @php
            $alamat  = \App\Models\Setting::get('toko_alamat');
            $kota    = \App\Models\Setting::get('toko_kota');
            $telepon = \App\Models\Setting::get('toko_telepon');
        @endphp
        @if($alamat || $telepon)
        <div class="text-center space-y-1.5">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Informasi Toko</h3>
            @if($alamat)
            <p class="text-sm text-gray-500">
                <span class="font-medium text-gray-700">Alamat:</span>
                {{ $alamat }}{{ $kota ? ', '.$kota : '' }}
            </p>
            @endif
            @if($telepon)
            <p class="text-sm text-gray-500">
                <span class="font-medium text-gray-700">Telepon:</span>
                {{ $telepon }}
            </p>
            @endif
        </div>
        @endif

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════════ --}}
<footer class="bg-white border-t border-gray-100">
    <div class="max-w-5xl mx-auto px-4 py-5 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-400">
        <span>&copy; {{ date('Y') }} {{ $tokoNama }}. All rights reserved.</span>
        <div class="flex gap-4">
            <a href="#tentang" class="hover:text-gray-600 transition">Tentang</a>
            <a href="{{ route('absensi.public') }}" class="hover:text-gray-600 transition">Absensi</a>
            <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:text-blue-700 transition">Login →</a>
        </div>
    </div>
</footer>

</body>
</html>
