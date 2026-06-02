<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('purchases.show', $purchase) }}"
               class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-xl font-semibold text-gray-800">Edit Pembelian</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('purchases.update', $purchase) }}"
                  x-data="purchaseForm({{ json_encode($purchase->items->map(fn($i) => [
                      'product_id'   => $i->product_id,
                      'product_name' => $i->product->name,
                      'unit_id'      => $i->unit_id,
                      'units'        => $i->product->units->map(fn($u) => [
                          'id'        => $u->id,
                          'unit_name' => $u->unit_name,
                          'is_base'   => $u->is_base,
                      ]),
                      'qty'          => (float) $i->qty,
                      'buy_price'    => (float) $i->buy_price,
                      'subtotal'     => (float) $i->subtotal,
                  ])) }})"
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
                    <h3 class="font-medium text-gray-700 mb-4">Informasi Pembelian</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                No. Faktur <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="invoice_number"
                                   value="{{ old('invoice_number', $purchase->invoice_number) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500
                                          @error('invoice_number') border-red-400 @enderror">
                            @error('invoice_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="purchase_date"
                                   value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500
                                          @error('purchase_date') border-red-400 @enderror">
                            @error('purchase_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                <a href="{{ route('suppliers.index') }}" target="_blank"
                                   class="text-xs text-blue-600 hover:text-blue-800">
                                    Kelola Supplier →
                                </a>
                            </div>
                            <select name="supplier_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                           focus:ring-2 focus:ring-blue-500">
                                <option value="">— Tanpa Supplier —</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}"
                                        @selected(old('supplier_id', $purchase->supplier_id) == $s->id)>
                                    {{ $s->name }}{{ $s->phone ? ' ('.$s->phone.')' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <input type="text" name="notes"
                                   value="{{ old('notes', $purchase->notes) }}"
                                   placeholder="Opsional..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Pembeli
                                <span class="text-gray-400 font-normal text-xs">(opsional — ditampilkan di nota)</span>
                            </label>
                            <input type="text" name="buyer_name"
                                   value="{{ old('buyer_name', $purchase->buyer_name) }}"
                                   placeholder="Nama pembeli jika ingin dicantumkan..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                          focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-medium text-gray-700">Item Pembelian</h3>
                        <div class="flex gap-2">
                            <input type="text" x-model="barcodeInput" @keydown.enter.prevent="scanBarcode"
                                   placeholder="Scan barcode..."
                                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm w-48
                                          focus:ring-2 focus:ring-blue-500">
                            <button type="button" @click="scanBarcode"
                                    class="px-3 py-1.5 bg-gray-100 border border-gray-300 text-gray-700
                                           text-sm rounded-lg hover:bg-gray-200 transition">
                                Scan
                            </button>
                        </div>
                    </div>

                    @error('items')
                    <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                    @enderror

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-left px-3 py-2 font-medium text-gray-600 min-w-[220px]">Produk</th>
                                    <th class="text-left px-3 py-2 font-medium text-gray-600 w-32">Satuan</th>
                                    <th class="text-right px-3 py-2 font-medium text-gray-600 w-28">Qty</th>
                                    <th class="text-right px-3 py-2 font-medium text-gray-600 w-36">Harga Beli</th>
                                    <th class="text-right px-3 py-2 font-medium text-gray-600 w-32">Subtotal</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, idx) in items" :key="item._key">
                                    <tr class="border-b border-gray-100">
                                        <td class="px-3 py-2">
                                            <input type="hidden" :name="'items['+idx+'][product_id]'" :value="item.product_id">
                                            <div class="relative">
                                                <input type="text"
                                                       x-model="item.product_name"
                                                       @input.debounce.300ms="searchProduct(idx, $event.target.value)"
                                                       @focus="item.showSuggestions = true"
                                                       @blur.window="closeSuggestions(idx)"
                                                       placeholder="Ketik nama produk..."
                                                       class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm
                                                              focus:ring-2 focus:ring-blue-500">
                                                <div x-show="item.showSuggestions && item.suggestions.length > 0"
                                                     class="absolute z-20 mt-1 w-full bg-white border border-gray-200
                                                            rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                                    <template x-for="p in item.suggestions" :key="p.id">
                                                        <button type="button"
                                                                @mousedown.prevent="selectProduct(idx, p)"
                                                                class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 transition">
                                                            <span class="font-medium" x-text="p.name"></span>
                                                            <span class="text-gray-400 text-xs ml-2" x-text="p.sku"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="hidden" :name="'items['+idx+'][unit_id]'" :value="item.unit_id">
                                            <select x-model="item.unit_id"
                                                    @change="onUnitChange(idx)"
                                                    class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm
                                                           focus:ring-2 focus:ring-blue-500">
                                                <option value="">— pilih —</option>
                                                <template x-for="u in item.units" :key="u.id">
                                                    <option :value="u.id" x-text="u.unit_name"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" :name="'items['+idx+'][qty]'"
                                                   x-model="item.qty"
                                                   @input="calcSubtotal(idx)"
                                                   min="0.0001" step="any"
                                                   class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm
                                                          text-right focus:ring-2 focus:ring-blue-500">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" :name="'items['+idx+'][buy_price]'"
                                                   x-model="item.buy_price"
                                                   @input="calcSubtotal(idx)"
                                                   min="0" step="100"
                                                   class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm
                                                          text-right focus:ring-2 focus:ring-blue-500">
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800"
                                            x-text="formatRp(item.subtotal)"></td>
                                        <td class="px-2 py-2 text-center">
                                            <button type="button" @click="removeItem(idx)"
                                                    x-show="items.length > 1"
                                                    class="text-red-400 hover:text-red-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="px-3 py-3 text-right font-semibold text-gray-700">Total</td>
                                    <td class="px-3 py-3 text-right font-bold text-gray-900 text-base"
                                        x-text="formatRp(grandTotal)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <button type="button" @click="addItem"
                            class="mt-3 flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Baris
                    </button>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('purchases.show', $purchase) }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="draft"
                                class="px-5 py-2.5 border border-blue-600 text-blue-600 text-sm rounded-lg
                                       hover:bg-blue-50 transition">
                            Simpan Draft
                        </button>
                        @can('pembelian.konfirmasi')
                        <button type="submit" name="action" value="confirm"
                                class="px-5 py-2.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                            Simpan &amp; Konfirmasi
                        </button>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
function purchaseForm(initialItems) {
    return {
        barcodeInput: '',
        items: [],
        _keyCounter: 0,

        get grandTotal() {
            return this.items.reduce((s, i) => s + (parseFloat(i.subtotal) || 0), 0);
        },

        init() {
            if (initialItems && initialItems.length > 0) {
                initialItems.forEach(i => {
                    this.items.push({
                        _key: ++this._keyCounter,
                        product_id:    i.product_id,
                        product_name:  i.product_name,
                        unit_id:       i.unit_id,
                        units:         i.units,
                        qty:           i.qty,
                        buy_price:     i.buy_price,
                        subtotal:      i.subtotal,
                        suggestions:   [],
                        showSuggestions: false,
                    });
                });
            } else {
                this.addItem();
            }
        },

        newItem() {
            return {
                _key: ++this._keyCounter,
                product_id: '', product_name: '', unit_id: '',
                units: [], qty: '', buy_price: '', subtotal: 0,
                suggestions: [], showSuggestions: false,
            };
        },

        addItem()    { this.items.push(this.newItem()); },
        removeItem(idx) { this.items.splice(idx, 1); },

        calcSubtotal(idx) {
            const item = this.items[idx];
            item.subtotal = (parseFloat(item.qty) || 0) * (parseFloat(item.buy_price) || 0);
        },
        onUnitChange(idx) { this.calcSubtotal(idx); },

        formatRp(val) {
            return 'Rp ' + (parseFloat(val) || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
        },

        async searchProduct(idx, q) {
            if (q.length < 2) { this.items[idx].suggestions = []; return; }
            try {
                const res  = await fetch(`{{ route('api.products.search') }}?q=${encodeURIComponent(q)}`);
                const data = await res.json();
                this.items[idx].suggestions = data;
            } catch(e) {}
        },

        closeSuggestions(idx) {
            setTimeout(() => { this.items[idx].showSuggestions = false; }, 150);
        },

        selectProduct(idx, product) {
            const item = this.items[idx];
            item.product_id   = product.id;
            item.product_name = product.name;
            item.units        = product.units;
            item.unit_id      = product.units.find(u => u.is_base)?.id ?? (product.units[0]?.id ?? '');
            item.suggestions  = [];
            item.showSuggestions = false;
        },

        async scanBarcode() {
            const barcode = this.barcodeInput.trim();
            if (!barcode) return;
            try {
                const res = await fetch(`{{ url('api/barcodes') }}/${encodeURIComponent(barcode)}`);
                if (!res.ok) { alert('Barcode tidak ditemukan.'); return; }
                const data = await res.json();
                const p = data.product; const u = data.unit;
                let idx = this.items.findIndex(i => !i.product_id);
                if (idx === -1) { this.addItem(); idx = this.items.length - 1; }
                const item = this.items[idx];
                item.product_id   = p.id;
                item.product_name = p.name;
                item.units        = p.units;
                item.unit_id      = u.id;
                item.qty          = 1;
                item.buy_price    = '';
                this.calcSubtotal(idx);
                this.barcodeInput = '';
            } catch(e) { alert('Terjadi kesalahan saat membaca barcode.'); }
        },
    };
}
</script>
@endpush

</x-app-layout>
