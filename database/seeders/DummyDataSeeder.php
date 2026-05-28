<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\ProductPrice;
use App\Models\ProductUnit;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Users ────────────────────────────────────────────────────────────

        foreach ([
            ['email' => 'admin@demo.local',  'name' => 'Admin Demo',   'role' => 'admin'],
            ['email' => 'manager@demo.local', 'name' => 'Manager Demo', 'role' => 'manajer'],
            ['email' => 'kasir@demo.local',   'name' => 'Kasir Demo',   'role' => 'kasir'],
        ] as $d) {
            User::firstOrCreate(['email' => $d['email']], [
                'name' => $d['name'], 'password' => bcrypt('password'), 'is_active' => true,
            ])->syncRoles([$d['role']]);
        }

        // ── Supplier ─────────────────────────────────────────────────────────

        foreach ([
            ['name' => 'PT. HM Sampoerna',       'phone' => '021-5555-0001', 'contact_person' => 'Budi Santoso'],
            ['name' => 'UD. Maju Jaya',           'phone' => '0812-3456-789', 'contact_person' => 'Siti Rahayu'],
            ['name' => 'CV. Distributor Mandiri', 'phone' => '0811-2233-445', 'contact_person' => 'Ahmad Fauzi'],
        ] as $d) {
            Supplier::firstOrCreate(['name' => $d['name']], array_merge($d, ['is_active' => true]));
        }

        // ── Kategori ─────────────────────────────────────────────────────────

        $katRokok    = Category::firstOrCreate(['slug' => 'rokok'],    ['name' => 'Rokok',        'description' => 'Produk rokok dan tembakau']);
        $katMakanan  = Category::firstOrCreate(['slug' => 'makanan'],  ['name' => 'Makanan',      'description' => 'Makanan & minuman ringan']);
        $katMinuman  = Category::firstOrCreate(['slug' => 'minuman'],  ['name' => 'Minuman',      'description' => 'Minuman kemasan']);

        // ── Produk ───────────────────────────────────────────────────────────
        // Helper: buat produk hanya jika SKU belum ada

        $this->buatProduk(
            sku: 'PRD-0001',
            data: [
                'category_id' => $katRokok->id,
                'name'        => 'Rokok Sampoerna Mild',
                'description' => 'Rokok A Mild 16 batang',
                'min_stock'   => 10,
            ],
            /*
             * Satuan dasar: Kotak (index 0)
             * Slop = 10 Kotak  →  konversi = 10
             */
            units: [
                ['unit_name' => 'Kotak', 'conversion' => 1,  'is_base' => true,
                 'barcode' => '8999999100016',
                 'eceran' => ['price' => 28000,  'min_qty' => 1],
                 'grosir' => ['price' => 26500,  'min_qty' => 10]],

                ['unit_name' => 'Slop',  'conversion' => 10, 'is_base' => false,
                 'barcode' => '8999999100023',
                 'eceran' => ['price' => 270000, 'min_qty' => 1],
                 'grosir' => ['price' => 260000, 'min_qty' => 5]],
            ]
        );

        /*
         * Contoh: Mie Instan ABC
         * Satuan dasar: Pcs
         * Renceng = 10 Pcs   → konversi = 10
         * Box     = 40 Pcs   → konversi = 40  (Box berisi 4 renceng × 10 pcs)
         */
        $this->buatProduk(
            sku: 'PRD-0002',
            data: [
                'category_id' => $katMakanan->id,
                'name'        => 'Mie Instan ABC Goreng',
                'description' => 'Mie instan rasa ayam bawang',
                'min_stock'   => 20,
            ],
            units: [
                ['unit_name' => 'Pcs',     'conversion' => 1,  'is_base' => true,
                 'barcode' => '8999999200011',
                 'eceran' => ['price' => 3500,   'min_qty' => 1],
                 'grosir' => ['price' => 3200,   'min_qty' => 10]],

                ['unit_name' => 'Renceng', 'conversion' => 10, 'is_base' => false,
                 'barcode' => '8999999200028',
                 'eceran' => ['price' => 35000,  'min_qty' => 1],
                 'grosir' => ['price' => 32000,  'min_qty' => 4]],

                ['unit_name' => 'Box',     'conversion' => 40, 'is_base' => false,
                 'barcode' => '8999999200035',
                 'eceran' => ['price' => 140000, 'min_qty' => 1],
                 'grosir' => ['price' => 128000, 'min_qty' => 2]],
            ]
        );

        /*
         * Contoh: Air Mineral Aqua 600ml
         * Satuan dasar: Botol
         * Box = 24 Botol  → konversi = 24
         *
         * (Berbeda dengan produk lain yang pakai Box=40 atau Box=50)
         * Ini menunjukkan Box bisa punya konversi berbeda di tiap produk.
         */
        $this->buatProduk(
            sku: 'PRD-0003',
            data: [
                'category_id' => $katMinuman->id,
                'name'        => 'Air Mineral Aqua 600ml',
                'description' => 'Air mineral kemasan 600ml',
                'min_stock'   => 24,
            ],
            units: [
                ['unit_name' => 'Botol', 'conversion' => 1,  'is_base' => true,
                 'barcode' => '8999999300012',
                 'eceran' => ['price' => 4500,  'min_qty' => 1],
                 'grosir' => ['price' => 4000,  'min_qty' => 12]],

                ['unit_name' => 'Box',   'conversion' => 24, 'is_base' => false,
                 'barcode' => '8999999300029',
                 'eceran' => ['price' => 95000, 'min_qty' => 1],
                 'grosir' => ['price' => 88000, 'min_qty' => 2]],
            ]
        );

        /*
         * Contoh: Tisu Paseo 250 sheet
         * Satuan dasar: Pak
         * Box = 50 Pak  → konversi = 50
         *
         * Bandingkan: Aqua punya Box=24, Mie punya Box=40, Tisu punya Box=50.
         * Nama satuan sama ("Box") tapi konversi beda di tiap produk — fully supported.
         */
        $this->buatProduk(
            sku: 'PRD-0004',
            data: [
                'category_id' => $katMakanan->id,
                'name'        => 'Tisu Paseo 250 sheet',
                'description' => 'Tisu makan 250 lembar',
                'min_stock'   => 5,
            ],
            units: [
                ['unit_name' => 'Pak', 'conversion' => 1,  'is_base' => true,
                 'barcode' => '8999999400011',
                 'eceran' => ['price' => 8500,   'min_qty' => 1],
                 'grosir' => ['price' => 7800,   'min_qty' => 10]],

                ['unit_name' => 'Box', 'conversion' => 50, 'is_base' => false,
                 'barcode' => '8999999400028',
                 'eceran' => ['price' => 390000, 'min_qty' => 1],
                 'grosir' => ['price' => 365000, 'min_qty' => 1]],
            ]
        );
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    private function buatProduk(string $sku, array $data, array $units): void
    {
        if (Product::where('sku', $sku)->exists()) {
            return;
        }

        $product = Product::create(array_merge($data, [
            'sku'          => $sku,
            'base_unit_id' => null,
            'stock_qty'    => 0,
            'avg_cost'     => 0,
            'is_active'    => true,
        ]));

        $baseUnitId = null;

        foreach ($units as $u) {
            $unit = ProductUnit::create([
                'product_id' => $product->id,
                'unit_name'  => $u['unit_name'],
                'conversion' => $u['conversion'],
                'is_base'    => $u['is_base'],
            ]);

            if ($u['is_base']) {
                $baseUnitId = $unit->id;
            }

            if (!empty($u['barcode'])) {
                ProductBarcode::create([
                    'product_id' => $product->id,
                    'unit_id'    => $unit->id,
                    'barcode'    => $u['barcode'],
                ]);
            }

            if (!empty($u['eceran'])) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'unit_id'    => $unit->id,
                    'price_type' => 'eceran',
                    'price'      => $u['eceran']['price'],
                    'min_qty'    => $u['eceran']['min_qty'],
                ]);
            }

            if (!empty($u['grosir'])) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'unit_id'    => $unit->id,
                    'price_type' => 'grosir',
                    'price'      => $u['grosir']['price'],
                    'min_qty'    => $u['grosir']['min_qty'],
                ]);
            }
        }

        $product->update(['base_unit_id' => $baseUnitId]);
    }
}
