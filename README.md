# N2N Toko — Sistem Manajemen Grosir & Gudang

Aplikasi POS (Point of Sale) berbasis web yang dirancang khusus untuk operasional toko grosir, distributor, dan retail. Mengelola pembelian, penjualan, stok, dan keuangan dalam satu sistem terintegrasi.

**Stack Teknologi:** Laravel 12 · MySQL · Tailwind CSS · Alpine.js · Spatie Permission · Spatie ActivityLog

---

## 🚀 Fitur Utama

| Modul | Deskripsi |
|---|---|
| **Produk & Stok** | Kelola multi-satuan (Pcs/Dus/Karton), barcode per unit, harga eceran vs grosir, serta HPP otomatis (*Moving Average*). |
| **Pembelian (Stok Masuk)** | Alur pembelian dari supplier: Draft → Dipesan → Diterima. Mendukung pembayaran cicilan/hutang ke supplier. |
| **Penjualan (Kasir)** | Pencarian produk cepat via Nama/SKU/Barcode. Auto-grosir jika mencapai minimal kuantitas. Cetak struk thermal 80mm. |
| **Keuangan** | Laporan Laba Kotor & Bersih, rekap PPN, pengeluaran operasional, dan laporan omzet per periode. |
| **Shift Kasir** | Laporan penjualan per kasir untuk memudahkan serah terima shift (Rekap harian per kasir). |
| **Audit Trail** | Catat otomatis setiap aktivitas (tambah/edit/hapus) data penting beserta nilai sebelum & sesudahnya. |

---

## 🛠️ Instalasi & Setup

### Prasyarat
- PHP 8.2 ke atas
- Composer
- Node.js 20+ & NPM
- MySQL 8.0 / MariaDB 10.4+

### Langkah Instalasi (Lokal/Development)
1. **Clone & Install:**
   ```bash
   git clone https://github.com/kumbang-kobum/n2ntoko.git
   cd n2ntoko
   composer install
   npm install
   ```
2. **Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Database:**
   Buat database kosong di MySQL, sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di file `.env`.
   ```bash
   php artisan migrate --seed
   ```
4. **Build & Run:**
   ```bash
   npm run build
   php artisan serve
   ```
   Akses di `http://localhost:8000`. Login Default: `admin@tokoku.com` / `password`.

---

## 📖 Panduan Penggunaan (Workflow)

Agar sistem berjalan optimal, ikuti urutan setup berikut:

### 1. Setup Awal
*   **Kategori & Supplier:** Daftarkan kategori produk dan supplier langganan Anda.
*   **Master Produk:** Masukkan data produk. 
    *   **PENTING (Konsep Satuan):** Tentukan satu **Satuan Dasar** (unit terkecil, misal: Pcs). Satuan lain (misal: Dus) diisi dengan nilai **Konversi** terhadap satuan dasar (misal: 1 Dus = 14 Pcs, maka konversi Dus = 14).
    *   **Harga Grosir:** Atur "Min Qty Grosir" agar harga otomatis berubah di kasir saat mencapai jumlah tersebut.

### 2. Manajemen Stok (Pembelian)
*   Buat transaksi di menu **Pembelian**.
*   Status **Diterima** akan otomatis menambah stok produk dan mengupdate harga beli rata-rata (HPP).
*   Jika belum lunas, sisa tagihan akan tercatat sebagai hutang supplier yang bisa dicicil di kemudian hari.

### 3. Transaksi Kasir
*   Pilih **Pelanggan** (Umum/Grosir) untuk membedakan perlakuan harga jika perlu.
*   Gunakan **Barcode Scanner** untuk kecepatan transaksi.
*   Pilih metode pembayaran (Tunai/Debit/QRIS). Untuk tunai, sistem menghitung kembalian secara otomatis.

### 4. Penutupan Shift & Laporan
*   Gunakan **Laporan Per Kasir** untuk mencocokkan uang fisik di kasir dengan catatan sistem sebelum serah terima shift.
*   Pantau **Laporan Keuangan** untuk melihat pertumbuhan profit dan efisiensi pengeluaran operasional.

---

## 🔄 Panduan Update Aman dari GitHub

Gunakan prosedur ini untuk memperbarui aplikasi di server produksi agar tetap stabil dan data tetap aman:

```bash
# 1. Masuk ke mode pemeliharaan (opsional, agar user tidak akses saat update)
php artisan down

# 2. Ambil kode terbaru tanpa menghapus file lokal (seperti .env atau upload-an)
git pull origin main

# 3. Update dependensi PHP & JS
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 4. Update struktur database & hak akses
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force

# 5. Bersihkan cache untuk performa maksimal
php artisan optimize

# 6. Pastikan permission folder tetap benar (Linux/Ubuntu)
chown -R www-data:www-data .
chmod -R 775 storage bootstrap/cache

# 7. Aktifkan kembali aplikasi
php artisan up
```

**Tips Update:** Jika Anda melakukan perubahan kustom di kode program, pastikan untuk melakukan `git stash` sebelum `git pull` dan `git stash pop` setelahnya, atau gunakan branch kustom.

---

## 📄 Lisensi
Sistem ini bersifat Open Source di bawah lisensi [MIT](LICENSE).
