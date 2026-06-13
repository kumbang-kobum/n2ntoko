{{-- Partial: _form.blade.php  — dipakai di create & edit --}}
@php
    $isEdit   = isset($product) && $product->exists;
    $oldUnits = old('units');

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

<div x-data="productForm({{ json_encode($formUnits) }}, {{ $baseIndex }})" x-init="init()" class="space-y-5">

    {{-- ── Informasi Produk ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-3">Informasi Produk</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $isEdit ? $product->name : '') }}"
                    autofocus placeholder="Contoh: Rokok Sampoerna Mild"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    SKU / Kode Produk
                    <span class="text-gray-400 font-normal text-xs">(otomatis jika kosong)</span>
                </label>
                <input type="text" name="sku" value="{{ old('sku', $isEdit ? $product->sku : $sku ?? '') }}"
                    placeholder="{{ $sku ?? 'PRD-0001' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sku') border-red-400 @enderror">
                @error('sku')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

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

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Stok Minimum
                    <span class="text-gray-400 font-normal text-xs">(peringatan stok menipis)</span>
                </label>
                <input type="number" name="min_stock" min="0" step="1"
                    value="{{ old('min_stock', $isEdit ? $product->min_stock : 0) }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="2" placeholder="Keterangan tambahan (opsional)"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
            </div>

            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl self-end">
                <div>
                    <div class="text-sm font-medium text-gray-700">Produk Aktif</div>
                    <div class="text-xs text-gray-400">Non-aktif tidak muncul saat kasir</div>
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

    {{-- ── Satuan & Harga ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
        <div class="flex items-start justify-between border-b border-gray-100 pb-3">
            <div>
                <h3 class="font-semibold text-gray-700 text-sm">Satuan, Barcode & Harga</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Pilih <strong class="text-gray-600">satuan dasar</strong> (terkecil).
                    Satuan lain diisi <strong class="text-gray-600">berapa kali</strong> satuan dasar.
                    Contoh: Pcs=dasar, Renceng=10, Box=40 → 1 Box = 40 Pcs.
                </p>
            </div>
            <button type="button" @click="addUnit()"
                class="inline-flex items-center gap-1.5 text-sm text-blue-600 font-medium
                       hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Satuan
            </button>
        </div>

        @error('units')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        <input type="hidden" name="base_unit_index" :value="baseUnitIndex">

        <template x-for="(unit, index) in units" :key="index">
            <div class="rounded-xl border-2 transition"
                 :class="baseUnitIndex === index
                    ? 'border-blue-300 bg-blue-50/40'
                    : 'border-gray-100 bg-gray-50/50'">

                {{-- Baris 1: identitas satuan --}}
                <div class="flex items-center gap-3 px-4 pt-4 pb-3">
                    {{-- Radio satuan dasar --}}
                    <button type="button" @click="setBase(index)"
                            class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                            :class="baseUnitIndex === index ? 'border-blue-600 bg-blue-600' : 'border-gray-300 bg-white'">
                        <div class="w-2 h-2 rounded-full bg-white" x-show="baseUnitIndex === index"></div>
                    </button>

                    {{-- ID tersembunyi (dipakai saat update agar unit lama dikenali) --}}
                    <input type="hidden" :name="`units[${index}][id]`" :value="unit.id ?? ''">

                    {{-- Nama Satuan (dropdown enum) --}}
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs text-gray-400 mb-1">Nama Satuan *</label>
                        <div class="flex gap-2">
                            <select x-model="unit.selectValue"
                                    @change="onUnitSelect(index)"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">— Pilih Satuan —</option>
                                <optgroup label="Umum">
                                    <option value="Pcs">Pcs (satuan)</option>
                                    <option value="Buah">Buah</option>
                                    <option value="Lusin">Lusin (12 pcs)</option>
                                </optgroup>
                                <optgroup label="Kemasan">
                                    <option value="Sachet">Sachet</option>
                                    <option value="Bungkus">Bungkus</option>
                                    <option value="Pak">Pak</option>
                                    <option value="Kotak">Kotak</option>
                                    <option value="Slop">Slop</option>
                                    <option value="Renceng">Renceng</option>
                                    <option value="Dus">Dus</option>
                                    <option value="Karton">Karton</option>
                                </optgroup>
                                <optgroup label="Botol & Kaleng">
                                    <option value="Botol">Botol</option>
                                    <option value="Kaleng">Kaleng</option>
                                    <option value="Galon">Galon</option>
                                </optgroup>
                                <optgroup label="Berat & Volume">
                                    <option value="Kg">Kg</option>
                                    <option value="Gram">Gram</option>
                                    <option value="Ons">Ons</option>
                                    <option value="Liter">Liter</option>
                                    <option value="Ml">Ml</option>
                                </optgroup>
                                <optgroup label="Lainnya">
                                    <option value="Roll">Roll</option>
                                    <option value="Meter">Meter</option>
                                    <option value="Ikat">Ikat</option>
                                    <option value="__lainnya__">Lainnya (ketik manual)...</option>
                                </optgroup>
                            </select>
                            {{-- Input manual jika pilih "Lainnya" --}}
                            <input type="text" x-show="unit.customUnit" x-model="unit.unit_name"
                                   placeholder="Nama satuan..."
                                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <input type="hidden" :name="`units[${index}][unit_name]`" :value="unit.unit_name">
                        </div>
                    </div>

                    {{-- Konversi --}}
                    <div class="w-40 shrink-0">
                        <label class="block text-xs text-gray-400 mb-1">Konversi ke Dasar *</label>
                        <div class="relative">
                            <input type="number" :name="`units[${index}][conversion]`" x-model="unit.conversion"
                                   :readonly="baseUnitIndex === index"
                                   step="0.0001" min="0.0001" placeholder="1"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                   :class="baseUnitIndex === index ? 'bg-gray-100 cursor-not-allowed text-gray-500' : 'bg-white'">
                            <span x-show="baseUnitIndex === index"
                                  class="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-blue-500 font-bold bg-blue-50 px-1 rounded">
                                DASAR
                            </span>
                        </div>
                        <p class="text-xs text-blue-600 mt-1 font-medium" x-show="baseUnitIndex !== index">
                            1 <span x-text="(unit.customUnit ? unit.unit_name : unit.selectValue) || '…'"></span>
                            = <span x-text="unit.conversion || '?'"></span>
                            <span x-text="units[baseUnitIndex]?.unit_name || 'dasar'"></span>
                        </p>
                    </div>

                    {{-- Barcode --}}
                    <div class="w-44 shrink-0">
                        <label class="block text-xs text-gray-400 mb-1">Barcode</label>
                        <input type="text" :name="`units[${index}][barcode]`" x-model="unit.barcode"
                               placeholder="Scan / ketik barcode"
                               @keydown.enter.prevent
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    </div>

                    {{-- Hapus --}}
                    <button type="button" x-show="units.length > 1" @click="removeUnit(index)"
                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg
                                   text-gray-300 hover:text-red-500 hover:bg-red-50 transition mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Baris 2: harga --}}
                <div class="flex items-center gap-4 px-4 pb-4 pt-1 border-t border-gray-100/80">
                    <div class="w-5 shrink-0"></div>{{-- spacer radio --}}
                    <div class="flex flex-1 gap-4">
                        {{-- Harga Eceran --}}
                        <div class="flex-1">
                            <label class="block text-xs text-gray-400 mb-1">Harga Eceran</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                <input type="number" :name="`units[${index}][price_eceran]`"
                                       x-model="unit.price_eceran" min="0" step="100" placeholder="0"
                                       class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                        </div>

                        {{-- Harga Grosir --}}
                        <div class="flex-1">
                            <label class="block text-xs text-gray-400 mb-1">Harga Grosir</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                <input type="number" :name="`units[${index}][price_grosir]`"
                                       x-model="unit.price_grosir" min="0" step="100" placeholder="0"
                                       class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                        </div>

                        {{-- Min Qty Grosir --}}
                        <div class="w-36 shrink-0">
                            <label class="block text-xs text-gray-400 mb-1">Min. Qty Grosir</label>
                            <div class="flex items-center gap-1.5">
                                <input type="number" :name="`units[${index}][min_qty_grosir]`"
                                       x-model="unit.min_qty_grosir" min="1" step="1" placeholder="10"
                                       class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-center">
                                <span class="text-xs text-gray-400 shrink-0"
                                      x-text="(unit.customUnit ? unit.customUnitName : unit.unit_name) || 'pcs'"></span>
                            </div>
                        </div>
                    </div>
                    <div class="w-7 shrink-0"></div>{{-- spacer hapus --}}
                </div>

                {{-- Badge "Satuan Dasar" --}}
                <div x-show="baseUnitIndex === index"
                     class="px-4 pb-3 -mt-1">
                    <span class="inline-flex items-center gap-1 text-xs text-blue-600 font-medium
                                 bg-blue-100 px-2 py-0.5 rounded-full">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Satuan Dasar — stok dihitung dalam satuan ini
                    </span>
                </div>
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
        units: [],
        baseUnitIndex: initialBaseIndex || 0,

        init() {
            const presets = [
                'Pcs','Buah','Lusin','Sachet','Bungkus','Pak','Kotak','Slop','Renceng','Dus','Karton',
                'Botol','Kaleng','Galon','Kg','Gram','Ons','Liter','Ml','Roll','Meter','Ikat'
            ];
            this.units = (initialUnits || [{ id:'', unit_name:'', conversion:1, barcode:'', price_eceran:'', price_grosir:'', min_qty_grosir:1 }])
                .map(u => {
                    const isCustom = u.unit_name !== '' && !presets.includes(u.unit_name);
                    return {
                        ...u,
                        selectValue: isCustom ? '__lainnya__' : u.unit_name,
                        customUnit:  isCustom,
                    };
                });
            if (this.baseUnitIndex >= this.units.length) this.baseUnitIndex = 0;
        },

        addUnit() {
            this.units.push({
                id:'', unit_name:'', selectValue:'', conversion:1, barcode:'',
                price_eceran:'', price_grosir:'', min_qty_grosir:1, customUnit:false,
            });
        },

        removeUnit(index) {
            if (this.units.length <= 1) return;
            this.units.splice(index, 1);
            if (this.baseUnitIndex >= this.units.length) this.baseUnitIndex = this.units.length - 1;
            else if (this.baseUnitIndex > index) this.baseUnitIndex--;
        },

        setBase(index) {
            this.baseUnitIndex = index;
            this.units[index].conversion = 1;
        },

        onUnitSelect(index) {
            const unit = this.units[index];
            if (unit.selectValue === '__lainnya__') {
                unit.customUnit = true;
                unit.unit_name  = '';
            } else {
                unit.customUnit = false;
                unit.unit_name  = unit.selectValue;
            }
        },
    }
}
</script>
@endpush
