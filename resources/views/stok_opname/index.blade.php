<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stok Opname</h2>
                <p class="text-sm text-gray-400 mt-0.5">Sesuaikan stok fisik dengan stok sistem</p>
            </div>
            <a href="{{ route('stok-opname.cetak') }}" target="_blank"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700
                      hover:bg-gray-50 px-4 py-2 rounded-xl text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Form
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
                <strong>Petunjuk:</strong>
                Isi kolom <em>Stok Fisik</em> dengan jumlah aktual hasil hitung di gudang (dalam satuan dasar).
                Kosongkan atau biarkan sama dengan sistem untuk produk yang tidak berubah.
                Klik <strong>Simpan Opname</strong> — hanya produk yang berbeda yang akan diperbarui.
            </div>

            <form method="POST" action="{{ route('stok-opname.store') }}"
                  x-data="opnameForm()"
                  @submit.prevent="submitForm">
                @csrf

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-700 text-sm">Daftar Produk</h3>
                        <div class="relative">
                            <input type="text" x-model="search" placeholder="Cari produk..."
                                class="pl-8 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300 focus:outline-none w-52">
                            <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Produk</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Satuan Dasar</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Stok Sistem</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase w-40">Stok Fisik</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Selisih</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($products as $i => $product)
                                <tr x-show="matchSearch('{{ addslashes($product->name) }}', '{{ addslashes($product->sku) }}')"
                                    :class="rows[{{ $i }}].changed ? 'bg-yellow-50' : ''">

                                    <input type="hidden" name="adjustments[{{ $i }}][product_id]"
                                           value="{{ $product->id }}">

                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ $product->sku }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-600">
                                        {{ $product->baseUnit?->unit_name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                                        {{ number_format($product->stock_qty, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number"
                                               name="adjustments[{{ $i }}][qty_fisik]"
                                               x-model="rows[{{ $i }}].qty_fisik"
                                               @input="calcSelisih({{ $i }}, {{ round($product->stock_qty, 4) }})"
                                               min="0" step="any"
                                               placeholder="{{ number_format($product->stock_qty, 2, ',', '.') }}"
                                               class="w-28 text-center border rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:outline-none transition"
                                               :class="rows[{{ $i }}].changed
                                                   ? (rows[{{ $i }}].selisih > 0 ? 'border-green-400 focus:ring-green-300 bg-green-50' : 'border-red-400 focus:ring-red-300 bg-red-50')
                                                   : 'border-gray-200 focus:ring-blue-300'">
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-medium">
                                        <span x-show="!rows[{{ $i }}].changed" class="text-gray-300">—</span>
                                        <span x-show="rows[{{ $i }}].changed && rows[{{ $i }}].selisih > 0"
                                              class="text-green-600">
                                            +<span x-text="rows[{{ $i }}].selisih.toLocaleString('id-ID')"></span>
                                        </span>
                                        <span x-show="rows[{{ $i }}].changed && rows[{{ $i }}].selisih < 0"
                                              class="text-red-500">
                                            <span x-text="rows[{{ $i }}].selisih.toLocaleString('id-ID')"></span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text"
                                               name="adjustments[{{ $i }}][notes]"
                                               placeholder="Keterangan (opsional)"
                                               class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                               :disabled="!rows[{{ $i }}].changed">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="text-sm text-gray-600">
                            <span x-text="changedCount"></span> produk akan diubah
                        </div>
                        <button type="submit"
                                :disabled="changedCount === 0"
                                :class="changedCount > 0
                                    ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer'
                                    : 'bg-gray-300 cursor-not-allowed'"
                                class="inline-flex items-center gap-2 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Opname
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
    function opnameForm() {
        return {
            search: '',
            rows: @json($products->map(fn($p) => ['qty_fisik' => '', 'selisih' => 0, 'changed' => false])),

            get changedCount() {
                return this.rows.filter(r => r.changed).length;
            },

            calcSelisih(i, stokSistem) {
                const val = this.rows[i].qty_fisik;
                if (val === '' || val === null) {
                    this.rows[i].changed = false;
                    this.rows[i].selisih = 0;
                } else {
                    const fisik = parseFloat(val);
                    const selisih = fisik - parseFloat(stokSistem);
                    this.rows[i].selisih = selisih;
                    this.rows[i].changed = Math.abs(selisih) >= 0.0001;
                }
            },

            matchSearch(name, sku) {
                if (!this.search) return true;
                const q = this.search.toLowerCase();
                return name.toLowerCase().includes(q) || sku.toLowerCase().includes(q);
            },

            submitForm() {
                // Nonaktifkan input baris yang tidak berubah agar tidak
                // melampaui batas max_input_vars PHP (default 1000)
                this.rows.forEach((row, i) => {
                    if (!row.changed) {
                        this.$el.querySelectorAll(`[name^="adjustments[${i}]"]`)
                            .forEach(el => el.disabled = true);
                    }
                });
                this.$el.submit();
            },
        };
    }
    </script>
</x-app-layout>
