{{--
    Partial: _form.blade.php
    Dipakai di create.blade.php dan edit.blade.php
    Variabel yang dibutuhkan: $product (null saat create), $categories, $sku (saat create)
--}}
@php
    $isEdit   = isset($product) && $product->exists;
    $oldUnits = old('units');

    // Bangun data unit dari old() input atau dari model
    if ($oldUnits) {
        $formUnits = $oldUnits;
        $baseIndex = old('base_unit_index', 0);
    } elseif ($isEdit) {
        $formUnits = $product->units->map(fn($u) => [
            'id'             => $u->id,
            'unit_name'      => $u->unit_name,
            'conversion'     => (float) $u->conversion,
            'barcode'        => $u->barcodes->first()?->barcode ?? '',
            'price_eceran'   => $u->prices->where('price_type','eceran')->first()?->price ?? '',
            'price_grosir'   => $u->prices->where('price_type','grosir')->first()?->price ?? '',
            'min_qty_grosir' => $u->prices->where('price_type','grosir')->first()?->min_qty ?? 1,
        ])->toArray();
        $baseIndex = $product->units->search(fn($u) => $u->is_base);
        $baseIndex = $baseIndex !== false ? $baseIndex : 0;
    } else {
        $formUnits = [['id'=>'','unit_name'=>'','conversion'=>1,'barcode'=>'','price_eceran'=>'','price_grosir'=>'','min_qty_grosir'=>1]];
        $baseIndex = 0;
    }
@endphp

<div
    x-data="productForm({{ json_encode($formUnits) }}, {{ $baseIndex }})"
    x-init="init()"
    class="space-y-5">

    {{-- ── Informasi Dasar ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-3">Informasi Produk</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Nama --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $product->name : '') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                    placeholder="Contoh: Sampoerna Mild" autofocus />
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- SKU --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    SKU / Kode Produk
                    <span class="text-gray-400 font-normal">(otomatis jika kosong)</span>
                </label>
                <input type="text" name="sku" value="{{ old('sku', $isEdit ? $product->sku : $sku ?? '') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sku') border-red-400 @enderror"
                    placeholder="{{ $sku ?? 'PRD-0001' }}" />
                @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                <select name="category_id"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Tanpa Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $isEdit ? $product->category_id : '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Min Stok --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Batas Stok Minimum
                    <span class="text-gray-400 font-normal">(peringatan stok menipis)</span>
                </label>
                <input type="number" name="min_stock" min="0" step="1"
                    value="{{ old('min_stock', $isEdit ? $product->min_stock : 0) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            {{-- Deskripsi --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Keterangan tambahan (opsional)">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
            </div>

            {{-- Status --}}
            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl md:col-span-2">
                <div>
                    <div class="text-sm font-medium text-gray-700">Produk Aktif</div>
                    <div class="text-xs text-gray-400">Produk non-aktif tidak muncul saat penjualan</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                        {{ old('is_active', $isEdit ? ($product->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600
                        after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white
                        after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- ── Satuan & Harga ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div>
                <h3 class="font-semibold text-gray-700 text-sm">Satuan, Barcode & Harga</h3>
                <p class="text-xs text-gray-400 mt-0.5">Tandai satu satuan sebagai <strong>satuan dasar</strong> (satuan terkecil untuk perhitungan stok)</p>
            </div>
            <button type="button" @click="addUnit()"
                class="inline-flex items-center gap-1.5 text-sm text-blue-600 font-medium hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Satuan
            </button>
        </div>

        @error('units')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror

        {{-- Input tersembunyi: base_unit_index --}}
        <input type="hidden" name="base_unit_index" :value="baseUnitIndex">

        {{-- Header kolom --}}
        <div class="hidden md:grid grid-cols-12 gap-3 text-xs font-semibold text-gray-400 uppercase tracking-wide px-1">
            <div class="col-span-2">Satuan Dasar</div>
            <div class="col-span-2">Nama Satuan</div>
            <div class="col-span-2">Konversi ke Dasar</div>
            <div class="col-span-2">Barcode</div>
            <div class="col-span-2">Harga Eceran</div>
            <div class="col-span-1">Harga Grosir</div>
            <div class="col-span-1"></div>
        </div>

        <template x-for="(unit, index) in units" :key="index">
            <div class="border border-gray-100 rounded-xl p-4 space-y-3 md:space-y-0"
                :class="baseUnitIndex === index ? 'border-blue-200 bg-blue-50/30' : 'bg-gray-50/50'">

                {{-- Mobile label --}}
                <div class="md:hidden flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Satuan #<span x-text="index+1"></span></span>
                    <button type="button" x-show="units.length > 1" @click="removeUnit(index)"
                        class="text-red-400 hover:text-red-600 text-xs">Hapus</button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-12 gap-3 items-start">

                    {{-- Satuan Dasar Radio --}}
                    <div class="col-span-2 md:col-span-2 flex md:justify-center items-start pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" class="sr-only" :checked="baseUnitIndex === index" @change="setBase(index)">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                                :class="baseUnitIndex === index ? 'border-blue-600 bg-blue-600' : 'border-gray-300 bg-white'"
                                @click="setBase(index)">
                                <div class="w-2 h-2 rounded-full bg-white" x-show="baseUnitIndex === index"></div>
                            </div>
                            <span class="text-xs text-gray-500 md:hidden">Satuan Dasar</span>
                        </label>
                    </div>

                    {{-- Nama Satuan --}}
                    <div class="col-span-2 md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Nama Satuan *</label>
                        <input type="text" :name="`units[${index}][unit_name]`" x-model="unit.unit_name"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                            placeholder="Kotak / Slop / Karton" />
                    </div>

                    {{-- Konversi --}}
                    <div class="col-span-2 md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Konversi ke Dasar *</label>
                        <div class="relative">
                            <input type="number" :name="`units[${index}][conversion]`" x-model="unit.conversion"
                                step="0.0001" min="0.0001"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                placeholder="1" />
                            <span x-show="baseUnitIndex === index"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-blue-500 font-medium">dasar</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 hidden md:block" x-show="baseUnitIndex !== index">
                            = <span x-text="unit.conversion"></span> <span x-text="units[baseUnitIndex]?.unit_name || 'dasar'"></span>
                        </p>
                    </div>

                    {{-- Barcode --}}
                    <div class="col-span-2 md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Barcode</label>
                        <input type="text" :name="`units[${index}][barcode]`" x-model="unit.barcode"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                            placeholder="Scan atau ketik..." />
                    </div>

                    {{-- Harga Eceran --}}
                    <div class="col-span-2 md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Harga Eceran</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                            <input type="number" :name="`units[${index}][price_eceran]`" x-model="unit.price_eceran"
                                min="0" step="100"
                                class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                placeholder="0" />
                        </div>
                    </div>

                    {{-- Harga Grosir + Min Qty --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs text-gray-500 mb-1 md:hidden">Harga Grosir</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                            <input type="number" :name="`units[${index}][price_grosir]`" x-model="unit.price_grosir"
                                min="0" step="100"
                                class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                                placeholder="0" />
                        </div>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs text-gray-400 whitespace-nowrap">min</span>
                            <input type="number" :name="`units[${index}][min_qty_grosir]`" x-model="unit.min_qty_grosir"
                                min="1" step="1"
                                class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white"
                                placeholder="10" />
                            <span class="text-xs text-gray-400">pcs</span>
                        </div>
                    </div>

                    {{-- Delete --}}
                    <div class="hidden md:flex md:col-span-1 justify-center items-start pt-1">
                        <button type="button" x-show="units.length > 1" @click="removeUnit(index)"
                            class="text-gray-300 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Konversi info pada mobile --}}
                <p class="text-xs text-gray-400 md:hidden" x-show="baseUnitIndex !== index">
                    1 <span x-text="unit.unit_name || 'satuan ini'"></span>
                    = <span x-text="unit.conversion"></span>
                    <span x-text="units[baseUnitIndex]?.unit_name || 'satuan dasar'"></span>
                </p>
            </div>
        </template>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('products.index') }}"
            class="border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
            Batal
        </a>
        <button type="submit"
            class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-blue-700 transition">
            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
function productForm(initialUnits, initialBaseIndex) {
    return {
        units: initialUnits || [{ id:'', unit_name:'', conversion:1, barcode:'', price_eceran:'', price_grosir:'', min_qty_grosir:1 }],
        baseUnitIndex: initialBaseIndex || 0,
        init() {
            // Pastikan baseUnitIndex tidak out of range
            if (this.baseUnitIndex >= this.units.length) this.baseUnitIndex = 0;
        },
        addUnit() {
            this.units.push({ id:'', unit_name:'', conversion:1, barcode:'', price_eceran:'', price_grosir:'', min_qty_grosir:1 });
        },
        removeUnit(index) {
            if (this.units.length <= 1) return;
            this.units.splice(index, 1);
            if (this.baseUnitIndex >= this.units.length) this.baseUnitIndex = this.units.length - 1;
            else if (this.baseUnitIndex > index) this.baseUnitIndex--;
        },
        setBase(index) {
            this.baseUnitIndex = index;
        }
    }
}
</script>
@endpush
