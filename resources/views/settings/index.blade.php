<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Pengaturan Toko</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PUT')

                {{-- Identitas Toko --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mb-5">
                    <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-3">Identitas Toko</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Toko <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="toko_nama"
                                   value="{{ old('toko_nama', $settings['toko_nama'] ?? '') }}"
                                   placeholder="Contoh: N2N Toko"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 @error('toko_nama') border-red-400 @enderror">
                            @error('toko_nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tagline / Slogan</label>
                            <input type="text" name="toko_tagline"
                                   value="{{ old('toko_tagline', $settings['toko_tagline'] ?? '') }}"
                                   placeholder="Contoh: Sistem Manajemen Grosir"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <input type="text" name="toko_telepon"
                                   value="{{ old('toko_telepon', $settings['toko_telepon'] ?? '') }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                            <input type="text" name="toko_kota"
                                   value="{{ old('toko_kota', $settings['toko_kota'] ?? '') }}"
                                   placeholder="Contoh: Jakarta"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="toko_alamat" rows="2"
                                      placeholder="Jalan, nomor, RT/RW, kelurahan, kecamatan..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">{{ old('toko_alamat', $settings['toko_alamat'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Struk --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mb-5">
                    <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-3">Pengaturan Struk & Nota</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Header Nota (opsional)</label>
                        <input type="text" name="nota_header"
                               value="{{ old('nota_header', $settings['nota_header'] ?? '') }}"
                               placeholder="Contoh: NPWP: 01.234.567.8-901.000"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Muncul di bawah nama toko pada struk 80mm</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Footer Struk</label>
                        <input type="text" name="struk_footer"
                               value="{{ old('struk_footer', $settings['struk_footer'] ?? 'Terima kasih atas kunjungan Anda!') }}"
                               placeholder="Terima kasih atas kunjungan Anda!"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Teks di bagian bawah struk setelah total pembayaran</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">PPN Default</label>
                        <div class="flex gap-3">
                            @foreach(['0' => 'Non-PPN (0%)', '11' => '11%', '12' => '12%'] as $val => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="ppn_default" value="{{ $val }}"
                                       {{ old('ppn_default', $settings['ppn_default'] ?? '0') == $val ? 'checked' : '' }}
                                       class="text-blue-600">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1">PPN yang terpilih secara otomatis saat membuka form kasir</p>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <input type="hidden" name="grosir_same_price_warning" value="0">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="grosir_same_price_warning" value="1"
                                   {{ old('grosir_same_price_warning', $settings['grosir_same_price_warning'] ?? '1') == '1' ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-700">Peringatan harga eceran = grosir</span>
                                <span class="block text-xs text-gray-400 mt-1">
                                    Tampilkan warna merah di kasir saat qty sudah masuk grosir, tetapi harga eceran dan grosir sama.
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Preview Struk --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 mb-5">
                    <h3 class="font-semibold text-gray-600 text-sm mb-3">Preview Struk (80mm)</h3>
                    <div class="font-mono text-xs bg-white border border-gray-200 rounded-lg p-4 max-w-xs">
                        <div class="text-center font-bold text-sm">{{ strtoupper($settings['toko_nama'] ?? 'NAMA TOKO') }}</div>
                        @if(!empty($settings['toko_tagline']))
                        <div class="text-center text-xs">{{ $settings['toko_tagline'] }}</div>
                        @endif
                        @if(!empty($settings['nota_header']))
                        <div class="text-center text-xs">{{ $settings['nota_header'] }}</div>
                        @endif
                        @if(!empty($settings['toko_alamat']))
                        <div class="text-center text-xs">{{ $settings['toko_alamat'] }}</div>
                        @endif
                        @if(!empty($settings['toko_telepon']))
                        <div class="text-center text-xs">Telp: {{ $settings['toko_telepon'] }}</div>
                        @endif
                        <div class="border-t border-dashed border-gray-300 my-2"></div>
                        <div class="text-xs text-gray-500">No  : JL-20260531-001</div>
                        <div class="text-xs text-gray-500">Tgl : 31/05/2026 08:30</div>
                        <div class="text-xs text-gray-500">Kasir: Admin</div>
                        <div class="border-t border-dashed border-gray-300 my-2"></div>
                        <div class="text-xs">Produk A</div>
                        <div class="flex justify-between text-xs"><span>1 Pcs × 10.000</span><span>10.000</span></div>
                        <div class="border-t border-dashed border-gray-300 my-2"></div>
                        <div class="flex justify-between text-xs font-bold"><span>TOTAL</span><span>10.000</span></div>
                        <div class="border-t border-dashed border-gray-300 my-2"></div>
                        <div class="text-center text-xs">{{ $settings['struk_footer'] ?? 'Terima kasih!' }}</div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Preview ini menggunakan data pengaturan yang tersimpan saat ini</p>
                </div>

                {{-- Bantuan & Kontak Developer --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mb-5">
                    <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-3">Bantuan & Laporan Bug</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor WhatsApp Developer
                        </label>
                        <input type="text" name="wa_developer"
                               value="{{ old('wa_developer', $settings['wa_developer'] ?? '') }}"
                               placeholder="Contoh: 6285768790777"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-400 mt-1">
                            Format internasional tanpa tanda +. Contoh: 6281234567890 (untuk 081234567890).
                            Digunakan pada menu <strong>Bantuan</strong> agar pengguna bisa lapor masalah langsung ke WhatsApp Anda.
                        </p>
                    </div>
                </div>

                @can('pengaturan.edit')
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition">
                        Simpan Pengaturan
                    </button>
                </div>
                @endcan

            </form>
        </div>
    </div>
</x-app-layout>
