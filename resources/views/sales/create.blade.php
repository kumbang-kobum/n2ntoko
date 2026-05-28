<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800">Kasir — Transaksi Baru</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-4">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" x-data="kasirForm()">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                    {{-- Kiri: Cart --}}
                    <div class="lg:col-span-2 space-y-4">

                        {{-- Scan & Cari --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                            <div class="flex gap-3">
                                <div class="flex-1 relative">
                                    <input type="text" x-model="searchQuery"
                                           @input.debounce.300ms="searchProduct"
                                           @focus="showSearch = true"
                                           @blur.window="setTimeout(()=>showSearch=false, 200)"
                                           @keydown.enter.prevent="if(results.length===1) addProduct(results[0])"
                                           placeholder="🔍 Cari nama / SKU produk..."
                                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm
                                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    {{-- Dropdown hasil cari --}}
                                    <div x-show="showSearch && results.length > 0"
                                         class="absolute z-30 w-full mt-1 bg-white border border-gray-200
                                                rounded-xl shadow-lg max-h-64 overflow-y-auto">
                                        <template x-for="p in results" :key="p.id">
                                            <button type="button" @mousedown.prevent="addProduct(p)"
                                                    class="w-full text-left px-4 py-3 hover:bg-blue-50 transition border-b border-gray-50 last:border-0">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="font-medium text-gray-800 text-sm" x-text="p.name"></span>
                                                        <span class="text-gray-400 text-xs ml-2" x-text="p.sku"></span>
                                                    </div>
                                                    <span class="text-xs text-gray-400">stok: <span x-text="p.stock_qty"></span></span>
                                                </div>
                                                <div class="flex gap-2 mt-1">
                                                    <template x-for="u in p.units" :key="u.id">
                                                        <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded"
                                                              x-text="u.unit_name + ' Rp' + formatNum(priceType==='grosir' ? u.price_grosir : u.price_eceran)"></span>
                                                    </template>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                {{-- Barcode --}}
                                <input type="text" x-model="barcodeInput"
                                       @keydown.enter.prevent="scanBarcode"
                                       placeholder="Scan barcode..."
                                       class="w-44 border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                              focus:ring-2 focus:ring-blue-500">
                                <button type="button" @click="scanBarcode"
                                        class="px-3 py-2 bg-gray-100 border border-gray-300 text-gray-700
                                               text-sm rounded-lg hover:bg-gray-200 transition">
                                    Scan
                                </button>
                            </div>
                        </div>

                        {{-- Tabel Cart --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="text-left px-4 py-2.5 font-medium text-gray-600">Produk</th>
                                        <th class="text-center px-3 py-2.5 font-medium text-gray-600 w-28">Satuan</th>
                                        <th class="text-right px-3 py-2.5 font-medium text-gray-600 w-24">Qty</th>
                                        <th class="text-right px-3 py-2.5 font-medium text-gray-600 w-32">Harga</th>
                                        <th class="text-right px-3 py-2.5 font-medium text-gray-600 w-32">Subtotal</th>
                                        <th class="w-8"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in items" :key="item._key">
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                            <td class="px-4 py-2">
                                                <input type="hidden" :name="'items['+idx+'][product_id]'" :value="item.product_id">
                                                <p class="font-medium text-gray-800 text-sm" x-text="item.product_name"></p>
                                                <p class="text-xs text-gray-400">
                                                    stok tersedia: <span x-text="item.stock_display" class="font-medium"></span>
                                                    <span x-text="item.unit_name"></span>
                                                </p>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="hidden" :name="'items['+idx+'][unit_id]'" :value="item.unit_id">
                                                <select x-model="item.unit_id" @change="onUnitChange(idx)"
                                                        class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs
                                                               focus:ring-2 focus:ring-blue-500 bg-white">
                                                    <template x-for="u in item.units" :key="u.id">
                                                        <option :value="u.id" x-text="u.unit_name"></option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" :name="'items['+idx+'][qty]'"
                                                       x-model="item.qty" @input="calcItem(idx)"
                                                       min="0.01" step="any"
                                                       class="w-full border border-gray-200 rounded-lg px-2 py-1 text-sm
                                                              text-right focus:ring-2 focus:ring-blue-500 bg-white">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" :name="'items['+idx+'][sell_price]'"
                                                       x-model="item.sell_price" @input="calcItem(idx)"
                                                       min="0" step="500"
                                                       class="w-full border border-gray-200 rounded-lg px-2 py-1 text-sm
                                                              text-right focus:ring-2 focus:ring-blue-500 bg-white">
                                            </td>
                                            <td class="px-3 py-2 text-right font-semibold text-gray-800"
                                                x-text="'Rp '+formatNum(item.subtotal)"></td>
                                            <td class="px-2 py-2">
                                                <button type="button" @click="removeItem(idx)"
                                                        class="text-gray-300 hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="items.length === 0">
                                        <tr>
                                            <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">
                                                Belum ada item. Cari produk atau scan barcode di atas.
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot x-show="items.length > 0" class="bg-gray-50 border-t-2 border-gray-200">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">TOTAL</td>
                                        <td class="px-3 py-3 text-right font-bold text-blue-600 text-base"
                                            x-text="'Rp '+formatNum(grandTotal)"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Kanan: Pembayaran --}}
                    <div class="space-y-4">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                            <h3 class="font-semibold text-gray-700">Detail Transaksi</h3>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">No. Faktur</label>
                                <input type="text" name="invoice_number"
                                       value="{{ old('invoice_number', $invoice) }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                              focus:ring-2 focus:ring-blue-500 font-mono">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                                <input type="date" name="sale_date"
                                       value="{{ old('sale_date', date('Y-m-d')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                              focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-2">Tipe Harga</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition"
                                           :class="priceType==='eceran' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                        <input type="radio" name="price_type" value="eceran" x-model="priceType"
                                               @change="updateAllPrices" class="sr-only">
                                        <span class="text-sm font-medium">Eceran</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition"
                                           :class="priceType==='grosir' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                        <input type="radio" name="price_type" value="grosir" x-model="priceType"
                                               @change="updateAllPrices" class="sr-only">
                                        <span class="text-sm font-medium">Grosir</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Catatan</label>
                                <input type="text" name="notes" placeholder="Opsional..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- Ringkasan & Bayar --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600 text-sm">Total Belanja</span>
                                <span class="font-bold text-lg text-gray-900" x-text="'Rp '+formatNum(grandTotal)"></span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Uang Diterima</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                                    <input type="number" name="paid_amount" x-model.number="paidAmount"
                                           @input="calcChange"
                                           min="0" step="500" placeholder="0"
                                           class="w-full border-2 border-gray-300 rounded-xl pl-9 pr-4 py-3 text-lg font-bold
                                                  text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                {{-- Quick buttons --}}
                                <div class="grid grid-cols-3 gap-2 mt-2">
                                    <template x-for="n in quickAmounts" :key="n">
                                        <button type="button" @click="paidAmount = n; calcChange()"
                                                class="text-xs py-1.5 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition"
                                                x-text="'Rp '+formatNum(n)"></button>
                                    </template>
                                </div>
                            </div>

                            <div class="flex justify-between items-center py-3 rounded-xl px-3"
                                 :class="change >= 0 ? 'bg-green-50' : 'bg-red-50'">
                                <span class="text-sm font-medium" :class="change >= 0 ? 'text-green-700' : 'text-red-700'">
                                    Kembalian
                                </span>
                                <span class="font-bold text-xl" :class="change >= 0 ? 'text-green-700' : 'text-red-700'"
                                      x-text="'Rp '+formatNum(Math.max(0,change))"></span>
                            </div>

                            <input type="hidden" name="paid_amount" :value="paidAmount">

                            <button type="submit"
                                    :disabled="items.length === 0 || paidAmount < grandTotal"
                                    class="w-full py-3.5 rounded-xl text-sm font-bold transition
                                           disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed
                                           bg-green-600 text-white hover:bg-green-700">
                                <span x-show="items.length === 0">Tambahkan item dulu</span>
                                <span x-show="items.length > 0 && paidAmount < grandTotal">
                                    Uang kurang Rp <span x-text="formatNum(grandTotal - paidAmount)"></span>
                                </span>
                                <span x-show="items.length > 0 && paidAmount >= grandTotal">
                                    ✓ Proses Pembayaran
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function kasirForm() {
    return {
        items:       [],
        searchQuery: '',
        barcodeInput:'',
        results:     [],
        showSearch:  false,
        priceType:  'eceran',
        paidAmount:  0,
        _key:        0,

        get grandTotal() { return this.items.reduce((s, i) => s + (parseFloat(i.subtotal)||0), 0); },
        get change()     { return this.paidAmount - this.grandTotal; },
        get quickAmounts() {
            const t = this.grandTotal;
            if (!t) return [10000,20000,50000];
            const ceil = (v, r) => Math.ceil(v/r)*r;
            return [ceil(t,1000), ceil(t,5000), ceil(t,10000)].filter((v,i,a) => a.indexOf(v)===i).slice(0,3);
        },

        init() {},

        formatNum(v) {
            return (parseFloat(v)||0).toLocaleString('id-ID', {maximumFractionDigits:0});
        },

        async searchProduct() {
            if (this.searchQuery.length < 2) { this.results = []; return; }
            try {
                const r = await fetch(`{{ route('api.sales.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                this.results = await r.json();
                this.showSearch = true;
            } catch(e) {}
        },

        async scanBarcode() {
            const b = this.barcodeInput.trim();
            if (!b) return;
            try {
                const r = await fetch(`{{ url('api/sales/barcode') }}/${encodeURIComponent(b)}`);
                if (!r.ok) { alert('Barcode tidak ditemukan atau stok habis.'); return; }
                const data = await r.json();
                this.addProductWithUnit(data.product, data.unit.id);
                this.barcodeInput = '';
            } catch(e) { alert('Gagal membaca barcode.'); }
        },

        addProduct(product) {
            const baseUnit = product.units.find(u => u.is_base) ?? product.units[0];
            this.addProductWithUnit(product, baseUnit?.id);
            this.searchQuery  = '';
            this.results      = [];
            this.showSearch   = false;
        },

        addProductWithUnit(product, unitId) {
            // Jika sudah ada di cart dengan unit yang sama, tambah qty
            const existing = this.items.find(i => i.product_id === product.id && i.unit_id == unitId);
            if (existing) { existing.qty++; this.calcItemObj(existing); return; }

            const unit  = product.units.find(u => u.id == unitId) ?? product.units[0];
            const price = this.priceType === 'grosir' ? unit.price_grosir : unit.price_eceran;

            this.items.push({
                _key:         ++this._key,
                product_id:   product.id,
                product_name: product.name,
                unit_id:      unit.id,
                unit_name:    unit.unit_name,
                units:        product.units,
                stock_qty:    product.stock_qty,
                stock_display:unit.stock_display ?? product.stock_qty,
                qty:          1,
                sell_price:   price,
                subtotal:     price,
            });
        },

        removeItem(idx) { this.items.splice(idx, 1); },

        onUnitChange(idx) {
            const item = this.items[idx];
            const unit = item.units.find(u => u.id == item.unit_id);
            if (!unit) return;
            item.unit_name    = unit.unit_name;
            item.stock_display= unit.stock_display ?? item.stock_qty;
            item.sell_price   = this.priceType === 'grosir' ? unit.price_grosir : unit.price_eceran;
            this.calcItem(idx);
        },

        calcItem(idx) { this.calcItemObj(this.items[idx]); },
        calcItemObj(item) { item.subtotal = (parseFloat(item.qty)||0) * (parseFloat(item.sell_price)||0); },

        updateAllPrices() {
            this.items.forEach(item => {
                const unit = item.units.find(u => u.id == item.unit_id);
                if (unit) item.sell_price = this.priceType === 'grosir' ? unit.price_grosir : unit.price_eceran;
                this.calcItemObj(item);
            });
        },

        calcChange() {},
    };
}
</script>
@endpush

</x-app-layout>
