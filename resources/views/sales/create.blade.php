<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}" class="text-gray-400 hover:text-gray-600 transition p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Kasir — Transaksi Baru</h2>
        </div>
    </x-slot>

    <div class="py-3 md:py-4">
        <div class="max-w-7xl mx-auto px-3 md:px-4 lg:px-8">

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-4">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" x-data="kasirForm({{ $ppnDefault }})">
                @csrf

                {{-- Layout: mobile=stack, tablet portrait=stack, tablet landscape/desktop=2col --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                    {{-- ═══ KIRI: CART ═══ --}}
                    <div class="lg:col-span-2 space-y-3">

                        {{-- Scan & Cari --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 md:p-4">
                            {{-- Baris 1: Search --}}
                            <div class="flex gap-2 mb-2">
                                <div class="flex-1 relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" x-model="searchQuery"
                                           @input.debounce.300ms="searchProduct"
                                           @focus="showSearch = true"
                                           @blur.window="setTimeout(()=>showSearch=false, 200)"
                                           @keydown.enter.prevent="if(results.length===1) addProduct(results[0])"
                                           placeholder="Cari nama / SKU produk..."
                                           class="w-full border border-gray-300 rounded-xl pl-9 pr-4 py-3 text-sm
                                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    {{-- Dropdown hasil cari --}}
                                    <div x-show="showSearch && results.length > 0"
                                         class="absolute z-30 w-full mt-1 bg-white border border-gray-200
                                                rounded-xl shadow-xl max-h-72 overflow-y-auto">
                                        <template x-for="p in results" :key="p.id">
                                            <button type="button" @mousedown.prevent="addProduct(p)"
                                                    class="w-full text-left px-4 py-3.5 hover:bg-blue-50 transition
                                                           border-b border-gray-50 last:border-0 active:bg-blue-100">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <span class="font-semibold text-gray-800 text-sm block truncate" x-text="p.name"></span>
                                                        <span class="text-gray-400 text-xs" x-text="p.sku"></span>
                                                    </div>
                                                    <span class="text-xs text-gray-400 shrink-0">stok: <span x-text="p.stock_qty"></span></span>
                                                </div>
                                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                                    <template x-for="u in p.units" :key="u.id">
                                                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full"
                                                              x-text="u.unit_name + ' · Rp' + formatNum(priceType==='grosir' ? u.price_grosir : u.price_eceran)"></span>
                                                    </template>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            {{-- Baris 2: Barcode --}}
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                    <input type="text" x-model="barcodeInput"
                                           @keydown.enter.prevent="scanBarcode"
                                           placeholder="Scan barcode atau ketik..."
                                           class="w-full border border-gray-300 rounded-xl pl-9 pr-4 py-3 text-sm
                                                  focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <button type="button" @click="scanBarcode"
                                        class="px-4 py-3 bg-gray-800 text-white text-sm font-medium rounded-xl
                                               hover:bg-gray-900 active:bg-black transition min-w-[56px]">
                                    Scan
                                </button>
                            </div>
                        </div>

                        {{-- Tabel Cart --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm min-w-[500px]">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-left px-4 py-3 font-medium text-gray-600">Produk</th>
                                            <th class="text-center px-2 py-3 font-medium text-gray-600 w-28">Satuan</th>
                                            <th class="text-right px-2 py-3 font-medium text-gray-600 w-20">Qty</th>
                                            <th class="text-right px-2 py-3 font-medium text-gray-600 w-36">Harga</th>
                                            <th class="text-right px-3 py-3 font-medium text-gray-600 w-28">Subtotal</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, idx) in items" :key="item._key">
                                            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                                                <td class="px-4 py-2.5">
                                                    <input type="hidden" :name="'items['+idx+'][product_id]'" :value="item.product_id">
                                                    <p class="font-medium text-gray-800 text-sm leading-tight" x-text="item.product_name"></p>
                                                    <p class="text-xs text-gray-400 mt-0.5">
                                                        Stok: <span x-text="item.stock_display" class="font-medium text-gray-600"></span>
                                                        <span x-text="item.unit_name"></span>
                                                    </p>
                                                </td>
                                                <td class="px-2 py-2.5">
                                                    <input type="hidden" :name="'items['+idx+'][unit_id]'" :value="item.unit_id">
                                                    <select x-model="item.unit_id" @change="onUnitChange(idx)"
                                                            class="w-full border border-gray-200 rounded-lg px-2 py-2 text-xs
                                                                   focus:ring-2 focus:ring-blue-500 bg-white">
                                                        <template x-for="u in item.units" :key="u.id">
                                                            <option :value="u.id" x-text="u.unit_name"></option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td class="px-2 py-2.5">
                                                    <input type="number" :name="'items['+idx+'][qty]'"
                                                           x-model="item.qty" @input="calcItem(idx)"
                                                           min="0.01" step="any"
                                                           class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm
                                                                  text-right focus:ring-2 focus:ring-blue-500 bg-white">
                                                </td>
                                                <td class="px-2 py-2.5">
                                                    <input type="number" :name="'items['+idx+'][sell_price]'"
                                                           x-model="item.sell_price" @input="calcItem(idx)"
                                                           min="0" step="500"
                                                           :class="item.auto_grosir ? 'border-purple-300 bg-purple-50' : 'border-gray-200 bg-white'"
                                                           class="w-full border rounded-lg px-2 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500">
                                                    <div x-show="item.auto_grosir" class="text-center mt-0.5">
                                                        <span class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded-full font-medium">★ Grosir</span>
                                                    </div>
                                                    <div x-show="!item.auto_grosir && item.min_qty_grosir > 0" class="text-center mt-0.5">
                                                        <span class="text-xs text-gray-400"
                                                              x-text="'≥'+item.min_qty_grosir+' grosir'"></span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2.5 text-right font-semibold text-gray-800 text-sm"
                                                    x-text="'Rp '+formatNum(item.subtotal)"></td>
                                                <td class="px-1 py-2.5 text-center">
                                                    <button type="button" @click="removeItem(idx)"
                                                            class="w-8 h-8 flex items-center justify-center mx-auto
                                                                   text-gray-300 hover:text-red-500 hover:bg-red-50
                                                                   rounded-lg transition active:bg-red-100">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="items.length === 0">
                                            <tr>
                                                <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-sm">
                                                    <div class="flex flex-col items-center gap-2">
                                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        Cari produk atau scan barcode di atas
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot x-show="items.length > 0" class="bg-gray-50 border-t-2 border-gray-200">
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-700">TOTAL</td>
                                            <td class="px-3 py-3 text-right font-bold text-blue-600 text-base"
                                                x-text="'Rp '+formatNum(grandTotal)"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- ═══ KANAN: PEMBAYARAN ═══ --}}
                    <div class="space-y-3">

                        {{-- Detail Transaksi --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                            <h3 class="font-semibold text-gray-700 text-sm mb-3">Detail Transaksi</h3>

                            {{-- No Faktur + Tanggal: 2 col pada md, 1 col pada lg --}}
                            <div class="grid grid-cols-2 lg:grid-cols-1 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">No. Faktur</label>
                                    <input type="text" name="invoice_number"
                                           value="{{ old('invoice_number', $invoice) }}"
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                                  focus:ring-2 focus:ring-blue-500 font-mono bg-gray-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                                    <input type="date" name="sale_date"
                                           value="{{ old('sale_date', date('Y-m-d')) }}"
                                           class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                                  focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Tipe Harga --}}
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-2">Tipe Harga</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 cursor-pointer transition active:scale-95"
                                           :class="priceType==='eceran' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600'">
                                        <input type="radio" name="price_type" value="eceran" x-model="priceType"
                                               @change="updateAllPrices" class="sr-only">
                                        <span class="text-sm font-semibold">Eceran</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 py-3 rounded-xl border-2 cursor-pointer transition active:scale-95"
                                           :class="priceType==='grosir' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-600'">
                                        <input type="radio" name="price_type" value="grosir" x-model="priceType"
                                               @change="updateAllPrices" class="sr-only">
                                        <span class="text-sm font-semibold">Grosir</span>
                                    </label>
                                </div>
                            </div>

                            {{-- PPN --}}
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-2">PPN</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="rate in taxOptions" :key="rate">
                                        <label class="flex items-center justify-center py-2.5 rounded-xl border-2 cursor-pointer transition active:scale-95 text-sm font-medium"
                                               :class="taxRate === rate ? 'border-yellow-500 bg-yellow-50 text-yellow-700' : 'border-gray-200 text-gray-600'">
                                            <input type="radio" x-model.number="taxRate" :value="rate" @change="calcTax" class="sr-only">
                                            <span x-text="rate === 0 ? 'Non-PPN' : rate + '%'"></span>
                                        </label>
                                    </template>
                                </div>
                                <input type="hidden" name="tax_rate" :value="taxRate">
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-2">Metode Bayar</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="flex flex-col items-center gap-1.5 py-3 rounded-xl border-2 cursor-pointer transition active:scale-95"
                                           :class="payMethod==='cash' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="payment_method" value="cash" x-model="payMethod" class="sr-only">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span class="text-xs font-semibold">Tunai</span>
                                    </label>
                                    <label class="flex flex-col items-center gap-1.5 py-3 rounded-xl border-2 cursor-pointer transition active:scale-95"
                                           :class="payMethod==='debit' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="payment_method" value="debit" x-model="payMethod" class="sr-only">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        <span class="text-xs font-semibold">Debit</span>
                                    </label>
                                    <label class="flex flex-col items-center gap-1.5 py-3 rounded-xl border-2 cursor-pointer transition active:scale-95"
                                           :class="payMethod==='qris' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-200 text-gray-500'">
                                        <input type="radio" name="payment_method" value="qris" x-model="payMethod" class="sr-only">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        <span class="text-xs font-semibold">QRIS</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Nama Pembeli --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Nama Pembeli
                                    <span class="text-gray-400 font-normal">(opsional)</span>
                                </label>
                                <input type="text" name="buyer_name"
                                       placeholder="Ketik nama pelanggan..."
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                              focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            </div>

                            {{-- Catatan --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                                <input type="text" name="notes" placeholder="Opsional..."
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                              focus:ring-2 focus:ring-blue-500 bg-gray-50">
                            </div>
                        </div>

                        {{-- Ringkasan & Bayar --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">

                            {{-- Summary --}}
                            <div class="space-y-1.5">
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <span>Subtotal</span>
                                    <span class="font-medium text-gray-700" x-text="'Rp '+formatNum(grandTotal)"></span>
                                </div>
                                <div x-show="taxRate > 0" class="flex justify-between items-center text-sm text-yellow-600">
                                    <span x-text="'PPN ' + taxRate + '%'"></span>
                                    <span class="font-medium" x-text="'Rp '+formatNum(taxAmount)"></span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                    <span class="font-bold text-gray-800">Total Bayar</span>
                                    <span class="font-bold text-xl text-gray-900" x-text="'Rp '+formatNum(grandTotal + taxAmount)"></span>
                                </div>
                            </div>

                            {{-- Input Tunai --}}
                            <div x-show="payMethod === 'cash'" class="space-y-2">
                                <label class="block text-xs font-medium text-gray-500">Uang Diterima</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">Rp</span>
                                    <input type="number" x-model.number="paidAmount" @input="calcChange"
                                           min="0" step="500" placeholder="0"
                                           class="w-full border-2 border-gray-300 rounded-xl pl-10 pr-4 py-3.5
                                                  text-xl font-bold text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                {{-- Quick amounts --}}
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="n in quickAmounts" :key="n">
                                        <button type="button" @click="paidAmount = n; calcChange()"
                                                class="py-3 text-sm font-medium border border-gray-200 rounded-xl
                                                       hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700
                                                       active:bg-blue-100 transition"
                                                x-text="'Rp '+formatNum(n)"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- Non-Tunai info --}}
                            <div x-show="payMethod !== 'cash'"
                                 class="py-3 px-4 bg-blue-50 rounded-xl text-sm text-blue-700 text-center font-medium">
                                <span x-text="payMethod === 'debit' ? '💳 Pembayaran via Debit/Transfer' : '📱 Pembayaran via QRIS'"></span>
                            </div>

                            {{-- Kembalian --}}
                            <div x-show="payMethod === 'cash'" class="flex justify-between items-center py-3 rounded-xl px-4"
                                 :class="change >= 0 ? 'bg-green-50' : 'bg-red-50'">
                                <span class="text-sm font-semibold" :class="change >= 0 ? 'text-green-700' : 'text-red-700'">Kembalian</span>
                                <span class="font-bold text-2xl" :class="change >= 0 ? 'text-green-700' : 'text-red-700'"
                                      x-text="'Rp '+formatNum(Math.max(0,change))"></span>
                            </div>

                            <input type="hidden" name="paid_amount" :value="payMethod !== 'cash' ? (grandTotal + taxAmount) : paidAmount">

                            <button type="submit"
                                    :disabled="items.length === 0 || (payMethod === 'cash' && paidAmount < grandTotal + taxAmount)"
                                    class="w-full py-4 rounded-xl font-bold text-base transition active:scale-95
                                           disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed
                                           bg-green-600 text-white hover:bg-green-700 shadow-sm">
                                <span x-show="items.length === 0">Tambahkan item terlebih dulu</span>
                                <span x-show="items.length > 0 && payMethod === 'cash' && paidAmount < grandTotal + taxAmount">
                                    Kurang Rp <span x-text="formatNum(grandTotal + taxAmount - paidAmount)"></span>
                                </span>
                                <span x-show="items.length > 0 && (payMethod !== 'cash' || paidAmount >= grandTotal + taxAmount)">
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
function kasirForm(defaultTaxRate = 0) {
    return {
        items:       [],
        searchQuery: '',
        barcodeInput:'',
        results:     [],
        showSearch:  false,
        priceType:   'eceran',
        payMethod:   'cash',
        taxRate:     defaultTaxRate,
        taxAmount:   0,
        taxOptions:  [0, 11, 12],
        paidAmount:  0,
        _key:        0,

        get grandTotal() { return this.items.reduce((s, i) => s + (parseFloat(i.subtotal)||0), 0); },
        get change()     { return this.paidAmount - (this.grandTotal + this.taxAmount); },
        get quickAmounts() {
            const t = this.grandTotal + this.taxAmount;
            if (!t) return [10000,20000,50000];
            const ceil = (v, r) => Math.ceil(v/r)*r;
            return [ceil(t,1000), ceil(t,5000), ceil(t,10000)].filter((v,i,a) => a.indexOf(v)===i).slice(0,3);
        },

        calcTax() {
            this.taxAmount = Math.round(this.grandTotal * this.taxRate / 100);
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
            this.searchQuery = '';
            this.results     = [];
            this.showSearch  = false;
        },

        addProductWithUnit(product, unitId) {
            const existing = this.items.find(i => i.product_id === product.id && i.unit_id == unitId);
            if (existing) {
                existing.qty++;
                this.calcItemObj(existing);
                this.autoPrice(existing);
                this.calcTax();
                return;
            }

            const unit  = product.units.find(u => u.id == unitId) ?? product.units[0];
            const price = this.resolvePrice(unit, 1);

            this.items.push({
                _key:          ++this._key,
                product_id:    product.id,
                product_name:  product.name,
                unit_id:       unit.id,
                unit_name:     unit.unit_name,
                units:         product.units,
                stock_qty:     product.stock_qty,
                stock_display: unit.stock_display ?? product.stock_qty,
                qty:           1,
                sell_price:    price,
                subtotal:      price,
                auto_grosir:   false,
                min_qty_grosir:unit.min_qty_grosir ?? 0,
            });
        },

        resolvePrice(unit, qty) {
            const minQty = unit.min_qty_grosir ?? 0;
            if (minQty > 0 && qty >= minQty && unit.price_grosir > 0) return unit.price_grosir;
            if (this.priceType === 'grosir' && unit.price_grosir > 0) return unit.price_grosir;
            return unit.price_eceran;
        },

        autoPrice(item) {
            const unit = item.units.find(u => u.id == item.unit_id);
            if (!unit) return;
            const minQty = unit.min_qty_grosir ?? 0;
            if (minQty > 0 && parseFloat(item.qty) >= minQty && unit.price_grosir > 0) {
                item.sell_price  = unit.price_grosir;
                item.auto_grosir = true;
            } else if (item.auto_grosir) {
                item.sell_price  = this.priceType === 'grosir' ? unit.price_grosir : unit.price_eceran;
                item.auto_grosir = false;
            }
            item.min_qty_grosir = minQty;
        },

        removeItem(idx) { this.items.splice(idx, 1); this.calcTax(); },

        onUnitChange(idx) {
            const item = this.items[idx];
            const unit = item.units.find(u => u.id == item.unit_id);
            if (!unit) return;
            item.unit_name      = unit.unit_name;
            item.stock_display  = unit.stock_display ?? item.stock_qty;
            item.min_qty_grosir = unit.min_qty_grosir ?? 0;
            item.auto_grosir    = false;
            item.sell_price     = this.resolvePrice(unit, parseFloat(item.qty) || 1);
            this.autoPrice(item);
            this.calcItemObj(item);
            this.calcTax();
        },

        calcItem(idx) {
            const item = this.items[idx];
            this.autoPrice(item);
            this.calcItemObj(item);
            this.calcTax();
        },

        calcItemObj(item) { item.subtotal = (parseFloat(item.qty)||0) * (parseFloat(item.sell_price)||0); },

        updateAllPrices() {
            this.items.forEach(item => {
                const unit = item.units.find(u => u.id == item.unit_id);
                if (!unit) return;
                if (!item.auto_grosir) {
                    item.sell_price = this.priceType === 'grosir' ? unit.price_grosir : unit.price_eceran;
                }
                this.autoPrice(item);
                this.calcItemObj(item);
            });
            this.calcTax();
        },

        calcChange() {},
    };
}
</script>
@endpush

@push('styles')
<style>
@media print {
    @page { size: 80mm auto; margin: 3mm; }
    .no-print, nav, header { display: none !important; }
    body > div { display: none !important; }
    .struk-80mm { display: block !important; }
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
@endpush

@if(request('print'))
@push('scripts')
<script>
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 400);
    });
</script>
@endpush
@endif

</x-app-layout>
