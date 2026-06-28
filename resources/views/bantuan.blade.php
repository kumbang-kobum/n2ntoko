<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Bantuan & Laporan Masalah</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Info Versi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">N2NToko</h3>
                        <p class="text-sm text-gray-500">Sistem POS Open Source untuk UMKM Indonesia</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-xs bg-blue-100 text-blue-700 font-medium px-2.5 py-0.5 rounded-full">
                                Versi {{ $versi }}
                            </span>
                            <span class="text-xs bg-green-100 text-green-700 font-medium px-2.5 py-0.5 rounded-full">
                                Open Source · MIT License
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Donasi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-700 text-sm">Dukung Pengembangan N2NToko</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    N2NToko dibuat dan dibagikan secara gratis dengan harapan bisa membantu sebanyak mungkin
                    UMKM di Indonesia. Jika sistem ini bermanfaat untuk usaha Anda, donasi sekecil apapun
                    sangat berarti untuk mendukung pengembangan fitur baru dan pemeliharaan sistem.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="shrink-0">
                        <img src="{{ asset('saweria.png') }}"
                             alt="QR Code Saweria KumbangKobum"
                             class="w-48 h-48 rounded-xl border border-gray-200 shadow-sm">
                    </div>
                    <div class="space-y-3 text-center sm:text-left">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-1">Via Saweria</p>
                            <p class="font-bold text-gray-800 text-lg">KumbangKobum</p>
                            <p class="text-sm text-gray-500">Scan QR code di samping menggunakan kamera HP</p>
                        </div>
                        <a href="https://saweria.co/KumbangKobum"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-gray-900 text-sm font-semibold rounded-xl transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                            </svg>
                            Donasi via Saweria
                        </a>
                        <p class="text-xs text-gray-400">Tidak wajib — N2NToko tetap gratis selamanya</p>
                    </div>
                </div>
            </div>

            {{-- Laporan Bug via WA --}}
            @if($waDeveloper)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.05 1.471 5.76L0 24l6.395-1.473A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.882a9.875 9.875 0 01-5.031-1.378l-.361-.214-3.741.981.998-3.648-.235-.374A9.861 9.861 0 012.118 12C2.118 6.533 6.533 2.118 12 2.118S21.882 6.533 21.882 12 17.467 21.882 12 21.882z"/>
                    </svg>
                    <h3 class="font-semibold text-gray-700 text-sm">Laporkan Masalah via WhatsApp</h3>
                </div>

                <p class="text-sm text-gray-600">
                    Jika menemukan error, fitur yang tidak berfungsi, atau ada saran perbaikan,
                    klik tombol di bawah. Pesan akan terisi otomatis dengan informasi toko dan versi aplikasi
                    sehingga masalah bisa ditangani lebih cepat.
                </p>

                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 text-xs text-gray-500 font-mono space-y-1">
                    <p class="text-gray-400 text-xs mb-2 font-sans font-medium">Isi pesan otomatis:</p>
                    <p>🏪 Toko &nbsp;&nbsp;: {{ $tokoNama }}</p>
                    <p>🖥️ Versi &nbsp;: N2NToko v{{ $versi }}</p>
                    <p>👤 User &nbsp;&nbsp;: {{ Auth::user()->name }} ({{ Auth::user()->getRoleNames()->first() ?? '-' }})</p>
                    <p>🕐 Waktu &nbsp;: <span id="wa-time">-</span></p>
                    <p>📝 Masalah: <em class="text-gray-400">[diisi oleh pengguna]</em></p>
                </div>

                <a id="wa-button" href="#"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-xl transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.089.534 4.05 1.471 5.76L0 24l6.395-1.473A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.882a9.875 9.875 0 01-5.031-1.378l-.361-.214-3.741.981.998-3.648-.235-.374A9.861 9.861 0 012.118 12C2.118 6.533 6.533 2.118 12 2.118S21.882 6.533 21.882 12 17.467 21.882 12 21.882z"/>
                    </svg>
                    Buka WhatsApp & Laporkan Masalah
                </a>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
                <strong>Nomor WhatsApp developer belum diatur.</strong><br>
                @can('pengaturan.edit')
                    Silakan isi di <a href="{{ route('settings.index') }}" class="underline font-medium">Pengaturan Toko → Bantuan & Laporan Bug</a>.
                @else
                    Hubungi admin toko untuk mengaktifkan fitur laporan masalah.
                @endcan
            </div>
            @endif

            {{-- Panduan Singkat --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-3">Tips Saat Melaporkan Masalah</h3>
                <ul class="text-sm text-gray-600 space-y-2.5">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 mt-0.5">✓</span>
                        <span>Jelaskan <strong>langkah-langkah</strong> yang dilakukan sebelum error terjadi</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 mt-0.5">✓</span>
                        <span>Sebutkan <strong>halaman</strong> atau menu yang bermasalah (contoh: "saat simpan penjualan")</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 mt-0.5">✓</span>
                        <span>Jika ada pesan error yang muncul di layar, <strong>kirimkan screenshot</strong>-nya</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 mt-0.5">✓</span>
                        <span>Apabila masalah terjadi berulang, sebutkan <strong>kapan pertama kali</strong> terjadi</span>
                    </li>
                </ul>
            </div>

            {{-- Kredit --}}
            <div class="text-center text-xs text-gray-400 py-2">
                N2NToko v{{ $versi }} — Open Source · MIT License &nbsp;·&nbsp;
                Dibuat dengan ❤️ untuk UMKM Indonesia
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const phone   = @json($waDeveloper);
            const toko    = @json($tokoNama);
            const versi   = @json($versi);
            const user    = @json(Auth::user()->name);
            const role    = @json(Auth::user()->getRoleNames()->first() ?? '-');

            const now     = new Date();
            const waktu   = now.toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' });

            document.getElementById('wa-time').textContent = waktu;

            const pesan =
                `Halo, saya ingin melaporkan masalah di N2NToko:\n\n` +
                `🏪 Toko   : ${toko}\n` +
                `🖥️ Versi  : N2NToko v${versi}\n` +
                `👤 User   : ${user} (${role})\n` +
                `🕐 Waktu  : ${waktu}\n\n` +
                `📝 Masalah:\n`;

            if (phone) {
                document.getElementById('wa-button').href =
                    `https://wa.me/${phone}?text=${encodeURIComponent(pesan)}`;
            }
        })();
    </script>
    @endpush

</x-app-layout>
