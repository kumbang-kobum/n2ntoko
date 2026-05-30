# N2N Toko — Sistem Manajemen Grosir & Gudang

Aplikasi POS (Point of Sale) berbasis web untuk mengelola pembelian, penjualan, stok, dan laporan laba rugi. Mendukung multi-satuan produk, harga grosir & eceran, barcode scanner, dan manajemen hak akses per peran.

**Stack:** Laravel 12 · Tailwind CSS · Alpine.js · Spatie Permission

---

## Fitur Utama

| Modul | Fitur |
|---|---|
| **Produk** | Multi-satuan (Pcs/Slop/Box/dll), barcode per satuan, harga eceran & grosir, stok minimum |
| **Pembelian** | Draft → Dipesan → Diterima, update HPP average cost otomatis |
| **Penjualan** | Kasir barcode scanner, pilih satuan, kembalian otomatis |
| **Pelanggan** | CRUD pelanggan, tipe Umum/Grosir/Langganan |
| **Supplier** | CRUD supplier, riwayat pembelian |
| **Stok Opname** | Penyesuaian stok fisik vs sistem, tercatat di kartu stok |
| **Pengeluaran** | Catat beban operasional (sewa, listrik, gaji, dll) |
| **Laporan** | Laba rugi harian, top produk, HPP otomatis per periode |
| **Pengguna** | CRUD user, assign role, aktif/nonaktif |

---

## Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL / SQLite

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone <repo-url> n2ntoko
cd n2ntoko

# 2. Install dependensi PHP
composer install

# 3. Install dependensi JavaScript
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=n2ntoko
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Jalankan migrasi & seeder
php artisan migrate --seed

# 8. Build aset frontend
npm run build

# 9. Jalankan server
php artisan serve
```

### Akun Default Setelah Seeder

| Email | Password | Role |
|---|---|---|
| admin@n2ntoko.com | admin123 | Admin |

> Segera ganti password setelah login pertama kali melalui menu **Profil Saya**.

---

## Menjalankan untuk Development

```bash
# Terminal 1 — Backend
php artisan serve

# Terminal 2 — Frontend (hot reload)
npm run dev
```

Buka browser: `http://localhost:8000`

---

## Panduan Penggunaan

### 1. Setup Awal (lakukan sekali)

#### A. Buat Kategori Produk
**Inventaris → Kategori** → tambahkan kategori seperti: Rokok, Minuman, Snack, Sembako, dll.

#### B. Daftarkan Supplier
**Inventaris → Supplier** → isi nama, telepon, dan kontak person distributor.

#### C. Tambah Produk dengan Multi-Satuan
**Inventaris → Produk → Tambah Produk**

Contoh setup **Rokok Sampoerna Mild 16**:

| # | Satuan | Konversi ke Dasar | Keterangan |
|---|---|---|---|
| 1 | **Pcs** *(Satuan Dasar)* | 1 | 1 bungkus rokok — stok dihitung di sini |
| 2 | Slop | 10 | 1 Slop = 10 Pcs |
| 3 | Box | 500 | 1 Box = 50 Slop × 10 Pcs |

> **Aturan penting:** Konversi selalu dihitung ke satuan dasar (terkecil yang dijual).
> Stok sistem disimpan dalam satuan dasar.

**Cara isi form satuan:**
1. Klik tombol **radio** di kiri baris → tandai sebagai Satuan Dasar
2. Pilih nama satuan dari dropdown atau ketik manual
3. Isi angka **konversi ke satuan dasar**
4. Isi **barcode** (scan atau ketik) untuk tiap satuan
5. Isi **harga eceran** dan **harga grosir** per satuan
6. Klik **+ Tambah Satuan** untuk baris berikutnya

---

### 2. Alur Pembelian (Stok Masuk)

**Inventaris → Pembelian → Tambah Pembelian**

```
Buat Draft  →  Dipesan (opsional)  →  Konfirmasi Diterima (stok masuk)
```

| Status | Stok Berubah? | Keterangan |
|---|---|---|
| Draft | ❌ | PO dalam proses pembuatan, bisa diedit |
| Dipesan | ❌ | Barang sudah dipesan ke supplier |
| Diterima | ✅ | Stok masuk, HPP diperbarui otomatis |

**Contoh beli 2 Box Sampoerna dari supplier:**
1. Tambah Pembelian → pilih supplier
2. Cari produk "Sampoerna" → pilih satuan **Box** → qty `2` → isi harga beli per Box
3. Simpan sebagai Draft atau langsung Konfirmasi
4. Setelah dikonfirmasi → stok bertambah **1.000 Pcs** (2 Box × 500)
5. HPP otomatis dihitung ulang dengan metode **Average Cost**

---

### 3. Alur Penjualan (Kasir)

**Penjualan → Transaksi Baru**

1. **Cari produk** — ketik nama/SKU di kolom pencarian, atau
2. **Scan barcode** — arahkan scanner ke barcode → produk otomatis masuk keranjang
3. **Pilih satuan** — ganti di dropdown (Pcs / Slop / Box sesuai kebutuhan)
4. **Pilih tipe harga** — Eceran atau Grosir (harga berubah otomatis)
5. **Input uang diterima** → kembalian dihitung otomatis
6. Klik **Proses Pembayaran**

> Stok langsung berkurang saat transaksi berhasil disimpan.

**Contoh jual ke warung 1 Slop Sampoerna:**
- Cari "Sampoerna" atau scan barcode slop
- Pilih satuan **Slop**, qty `1`, tipe harga **Grosir**
- Stok berkurang **10 Pcs**
- Profit dicatat otomatis: harga jual − (HPP × 10 Pcs)

---

### 4. Stok Opname

**Inventaris → Stok Opname**

Digunakan ketika stok fisik berbeda dengan stok di sistem (susut, rusak, salah input).

1. Hitung stok fisik di gudang
2. Isi kolom **Stok Fisik** untuk produk yang ada selisih
3. Klik **Simpan Penyesuaian**
4. Sistem otomatis catat selisih di kartu stok sebagai `adjustment`

---

### 5. Laporan

**Laporan** → pilih periode (Hari Ini / Minggu Ini / Bulan Ini / Kustom)

| Data | Keterangan |
|---|---|
| Total Penjualan | Omzet periode yang dipilih |
| HPP | Harga pokok semua barang terjual |
| Laba Kotor | Penjualan − HPP |
| Tabel Harian | Omzet, laba, dan margin per hari |
| Top 10 Produk | Produk terlaris berdasarkan omzet |

---

### 6. Pengeluaran Operasional

**Pengeluaran** → klik **Catat Pengeluaran**

Kategori: Sewa, Listrik, Air, Gaji, Transport, Pemeliharaan, Lainnya

Setiap catatan pengeluaran menyimpan tanggal, kategori, keterangan, jumlah, dan nomor bukti.

---

## Hak Akses Per Role

### Matrix Permission

| Permission | Admin | Manajer | Kasir | Gudang |
|---|:---:|:---:|:---:|:---:|
| produk.lihat | ✅ | ✅ | ✅ | ✅ |
| produk.tambah | ✅ | — | — | ✅ |
| produk.edit | ✅ | — | — | ✅ |
| produk.hapus | ✅ | — | — | — |
| pembelian.lihat | ✅ | ✅ | — | ✅ |
| pembelian.tambah | ✅ | — | — | ✅ |
| pembelian.edit | ✅ | — | — | ✅ |
| pembelian.konfirmasi | ✅ | — | — | ✅ |
| pembelian.hapus | ✅ | — | — | — |
| penjualan.lihat | ✅ | ✅ | ✅ | — |
| penjualan.tambah | ✅ | — | ✅ | — |
| penjualan.batal | ✅ | — | ✅ | — |
| pelanggan.lihat | ✅ | ✅ | ✅ | — |
| pelanggan.tambah | ✅ | — | ✅ | — |
| pelanggan.edit | ✅ | — | ✅ | — |
| pelanggan.hapus | ✅ | — | — | — |
| pengeluaran.lihat | ✅ | ✅ | — | — |
| pengeluaran.tambah | ✅ | — | — | — |
| pengeluaran.edit | ✅ | — | — | — |
| pengeluaran.hapus | ✅ | — | — | — |
| laporan.lihat | ✅ | ✅ | — | — |
| laporan.ekspor | ✅ | ✅ | — | — |
| user.lihat | ✅ | — | — | — |
| user.tambah | ✅ | — | — | — |
| user.edit | ✅ | — | — | — |
| user.hapus | ✅ | — | — | — |

### Deskripsi Role

| Role | Untuk Siapa |
|---|---|
| **Admin** | Pemilik / pengelola penuh — akses semua fitur |
| **Manajer** | Kepala toko — lihat semua data & laporan, tidak bisa ubah |
| **Kasir** | Petugas kasir — transaksi penjualan & kelola pelanggan |
| **Gudang** | Petugas gudang — kelola produk, pembelian, konfirmasi stok masuk |

### Menambah Role / Permission Kustom

Edit `database/seeders/RolePermissionSeeder.php`, tambahkan permission dan assign ke role, lalu:

```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan permission:cache-reset
```

---

## Konsep Penting

### HPP Average Cost (Rata-rata Bergerak)

```
HPP Baru = (Stok Lama × HPP Lama + Qty Masuk × Harga Beli Per Dasar)
           ─────────────────────────────────────────────────────────────
                           Stok Lama + Qty Masuk
```

Diperbarui otomatis setiap pembelian dikonfirmasi.

### Kartu Stok (Stock Ledger)

Setiap perubahan stok tercatat otomatis dengan tipe:

| Tipe | Dari |
|---|---|
| `in` | Pembelian dikonfirmasi |
| `out` | Penjualan |
| `adjustment` | Stok opname atau pembatalan penjualan |

### Konversi Satuan

```
Qty Base (stok berkurang/bertambah) = Qty Input × Konversi Satuan
```

Contoh: jual 2 Slop (konversi=10) → stok berkurang 20 (satuan dasar)

---

## Struktur Navigasi

```
/                → Welcome (belum login) | Dashboard (sudah login)
/dashboard       → Ringkasan omzet, laba, total produk, stok menipis
/products        → Daftar & kelola produk
/categories      → Kategori produk
/stok-opname     → Penyesuaian stok fisik
/suppliers       → Data supplier
/purchases       → Pembelian dari supplier
/sales           → Transaksi penjualan (kasir)
/customers       → Data pelanggan
/expenses        → Pengeluaran operasional
/laporan         → Laporan laba rugi
/users           → Manajemen pengguna & role
```

---

## Apakah Aplikasi Ini Bisa Digunakan Selain untuk Grosir?

**Ya — aplikasi ini fleksibel dan bisa digunakan untuk berbagai jenis usaha.**

Aplikasi ini dirancang dengan konsep umum manajemen stok dan penjualan, bukan terikat pada model bisnis grosir saja. Berikut kesesuaiannya:

| Jenis Usaha | Dukungan | Catatan |
|---|---|---|
| **Toko Grosir** | ✅ Penuh | Harga grosir/eceran, multi-satuan, pelanggan tipe grosir |
| **Penjualan Gudang / Distributor** | ✅ Penuh | Beli dalam Box/Karton, jual per Slop/Pcs ke warung/toko |
| **Toko Retail / Warung** | ✅ Penuh | Gunakan hanya satuan eceran, hilangkan harga grosir |
| **Toko Campuran (retail + grosir)** | ✅ Penuh | Pilih tipe harga Eceran/Grosir saat transaksi |
| **Mini Market** | ✅ Baik | Tambahkan lebih banyak kategori & produk |
| **Apotek / Toko Bahan** | ✅ Baik | Gunakan satuan berat (Kg, Gram, Ons, Liter) |
| **Toko dengan Kasir Banyak** | ✅ Baik | Buat akun kasir terpisah per orang |

### Untuk Penjualan Gudang (Distributor)

Alur yang cocok:
```
Beli dari pabrik/agen  →  Stok di gudang (dalam Box/Karton)
                       ↓
Jual ke warung/toko    →  Per Slop, Per Kotak, Per Pcs
                       ↓
Laporan omzet & laba   →  Per periode
```

Yang perlu disesuaikan:
1. **Nama role** — "Kasir" bisa diubah menjadi "Sales" atau "Admin Penjualan"
2. **Pelanggan** — gunakan tipe **Grosir** atau **Langganan** untuk warung/toko pelanggan tetap
3. **Satuan produk** — sesuaikan dengan kemasan gudang (Box, Karton, Karung, dll)
4. **Harga grosir** — aktifkan dan isi harga khusus untuk pelanggan grosir/langganan

### Yang Belum Tersedia (Roadmap)

| Fitur | Status |
|---|---|
| Cetak struk / nota penjualan | Belum ada |
| Laporan laba bersih (sudah kurangi pengeluaran) | Belum otomatis |
| Piutang pelanggan (jual kredit) | Belum ada |
| Hutang supplier (bayar cicilan) | Belum ada |
| Ekspor laporan ke Excel/PDF | Belum ada |
| Multi-cabang / multi-gudang | Belum ada |
