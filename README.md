# N2N Toko — Sistem Manajemen Grosir & Gudang

Aplikasi POS (Point of Sale) berbasis web yang dirancang khusus untuk operasional toko grosir, distributor, dan retail. Mengelola pembelian, penjualan, stok, dan keuangan dalam satu sistem terintegrasi.

**Stack Teknologi:** Laravel 12 · MySQL · Tailwind CSS · Alpine.js · Spatie Permission · Spatie ActivityLog

---

## Fitur Utama

| Modul | Deskripsi |
|---|---|
| **Produk & Stok** | Multi-satuan (Pcs/Dus/Karton), barcode per unit, harga eceran vs grosir, HPP otomatis (*Moving Average*). |
| **Pembelian** | Alur dari supplier: Draft → Dipesan → Diterima. Mendukung pembayaran cicilan/hutang ke supplier. |
| **Penjualan (Kasir)** | Pencarian via Nama/SKU/Barcode. Auto-grosir berdasarkan minimal kuantitas. Cetak struk thermal 80mm & dot-matrix LX-310. |
| **Keuangan** | Laporan Laba Kotor & Bersih, rekap PPN, pengeluaran operasional, omzet per periode. |
| **Stok Opname** | Penyesuaian stok fisik vs sistem dengan catatan selisih lengkap. |
| **Hak Akses** | Manajemen peran (Admin/Manajer/Kasir/Gudang) dengan kontrol permission per fitur. |
| **Audit Trail** | Riwayat aktivitas otomatis: siapa mengubah apa, kapan, nilai sebelum & sesudahnya. |

---

## Instalasi & Setup

### Prasyarat
- PHP 8.2+
- Composer
- Node.js 20+ & NPM
- MySQL 8.0 / MariaDB 10.4+

### Langkah Instalasi (Lokal / Development)

```bash
# 1. Clone & install dependensi
git clone https://github.com/kumbang-kobum/n2ntoko.git
cd n2ntoko
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD di .env, lalu:
php artisan migrate --seed

# 4. Build asset & jalankan server
npm run build
php artisan serve
```

Akses di `http://localhost:8000`. Login default: lihat `ADMIN_EMAIL` dan `ADMIN_PASSWORD` di `.env` (atau set terlebih dahulu sebelum seeding).

---

## Panduan Penggunaan (Workflow)

### 1. Setup Awal
- **Kategori & Supplier:** Daftarkan terlebih dahulu sebelum input produk.
- **Master Produk:** Tentukan satu **Satuan Dasar** (unit terkecil, misal: Pcs atau Kotak). Stok selalu dihitung dalam unit ini.

  **Logika Konversi** — hitung langsung ke satuan dasar, bukan berjenjang:

  | Satuan | Konversi | Penjelasan |
  |---|---|---|
  | Kotak | 1 | Satuan Dasar |
  | Slop | 10 | 1 Slop = 10 Kotak |
  | Dus | 120 | 1 Dus = 12 Slop × 10 Kotak |

- **Harga Grosir:** Atur "Min Qty Grosir" agar harga berubah otomatis di kasir saat mencapai jumlah tersebut.

### 2. Manajemen Stok (Pembelian)
- Buat transaksi di menu **Pembelian**.
- Status **Diterima** otomatis menambah stok dan mengupdate HPP rata-rata.
- Sisa tagihan tercatat sebagai hutang supplier yang bisa dilunasi kemudian.

### 3. Transaksi Kasir
- Gunakan **Barcode Scanner** untuk kecepatan transaksi.
- Pilih metode pembayaran: Tunai / Debit / QRIS. Tunai menghitung kembalian otomatis.
- Setelah transaksi, pilih format cetak: Struk 80mm atau LX-310 (nota lengkap / nota gudang).

### 4. Laporan & Penutupan Shift
- **Laporan Per Kasir** untuk rekonsiliasi uang fisik sebelum serah terima shift.
- **Laporan Keuangan** untuk pantau profit dan pengeluaran operasional.

---

## Update dari GitHub (Tanpa Kehilangan Data)

Gunakan prosedur ini setiap kali ada pembaruan kode. Data di database dan file upload **tidak akan terhapus** selama mengikuti langkah ini.

### File yang AMAN (tidak disentuh `git pull`)
| File / Direktori | Isi | Status saat update |
|---|---|---|
| `.env` | Konfigurasi DB, kredensial, kunci | **Tidak berubah** |
| `storage/app/` | File upload pengguna | **Tidak berubah** |
| `storage/logs/` | Log aplikasi | **Tidak berubah** |
| Database | Semua data transaksi & master | **Tidak berubah** |

### Langkah Update

```bash
# 1. Masuk ke direktori aplikasi
cd /www/wwwroot/grosir.n2n.com      # sesuaikan path server Anda

# 2. Aktifkan mode pemeliharaan (user melihat halaman "sedang pemeliharaan")
php artisan down

# 3. Backup database sebelum update (sangat disarankan)
mysqldump -u DB_USER -p DB_NAME > backup_$(date +%Y%m%d_%H%M%S).sql

# 4. Ambil kode terbaru dari GitHub
git pull origin main

# 5. Update dependensi PHP (tanpa package dev, dioptimasi untuk produksi)
composer install --no-dev --optimize-autoloader

# 6. Update dependensi JS & build asset
npm install && npm run build

# 7. Jalankan migrasi database yang baru (--force wajib di production)
php artisan migrate --force

# 8. Sinkronkan role & permission terbaru
php artisan db:seed --class=RolePermissionSeeder --force
php artisan permission:cache-reset

# 9. Perbaiki permission folder (PENTING — lihat detail di bawah)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 10. Bersihkan & rebuild semua cache
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan view:cache
php artisan config:cache
php artisan route:cache

# 11. Aktifkan kembali aplikasi
php artisan up
```

> **Catatan migrasi:** Perintah `migrate --force` hanya menjalankan file migrasi yang **belum pernah dijalankan**. Data yang sudah ada di tabel lain tidak tersentuh.

---

## Perbaikan Permission (Server Production)

Error seperti `tempnam(): file created in the system's temporary directory` atau `Permission denied` terjadi karena Laravel tidak bisa menulis ke direktori `storage/` atau `bootstrap/cache/`.

### Perintah Perbaikan

```bash
cd /www/wwwroot/grosir.n2n.com      # sesuaikan path

# Perbaiki kepemilikan dan permission
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Pre-compile semua Blade view ke cache agar tidak perlu tulis saat runtime
php artisan view:cache
```

### User Web Server yang Umum

| Web Server | User | Perintah chown |
|---|---|---|
| Apache (Ubuntu/Debian) | `www-data` | `chown -R www-data:www-data storage bootstrap/cache` |
| Nginx (Ubuntu/Debian) | `www-data` | `chown -R www-data:www-data storage bootstrap/cache` |
| Nginx (CentOS/RHEL) | `nginx` | `chown -R nginx:nginx storage bootstrap/cache` |
| Shared Hosting / CWP / AAPanel | `www` | `chown -R www:www storage bootstrap/cache` |
| PHP-FPM (cek aktif) | *(lihat di bawah)* | — |

Jika tidak yakin user yang digunakan, jalankan:
```bash
# Lihat user yang menjalankan proses web server
ps aux | grep -E 'nginx|apache|php-fpm' | grep -v grep | awk '{print $1}' | sort -u
```

### Direktori yang Wajib Writable

```
storage/
  app/            ← file upload
  framework/
    cache/        ← cache aplikasi
    sessions/     ← sesi login pengguna
    views/        ← compiled Blade templates  ← paling sering bermasalah
  logs/           ← log error

bootstrap/
  cache/          ← cache config, route, services
```

### Verifikasi Permission

```bash
# Cek status permission semua direktori penting
ls -la storage/framework/
ls -la storage/framework/views/
ls -la bootstrap/cache/

# Test: coba tulis file sementara
touch storage/framework/views/test_write && rm storage/framework/views/test_write \
  && echo "OK: views writable" || echo "GAGAL: views tidak writable"
```

### Reset Penuh Cache (jika masih bermasalah)

```bash
# Hapus semua file cache yang mungkin corrupt
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Reset cache permission Spatie
php artisan permission:cache-reset

# Rebuild ulang
php artisan optimize
```

---

## Troubleshooting

| Gejala | Kemungkinan Penyebab | Solusi |
|---|---|---|
| `tempnam(): file created in the system's temporary directory` | `storage/framework/views/` tidak writable | Jalankan `chown` + `chmod` di atas, lalu `php artisan view:cache` |
| Halaman blank / 500 setelah update | File cache lama tidak cocok dengan kode baru | `php artisan optimize:clear && php artisan optimize` |
| Permission baru tidak berlaku | Cache Spatie belum direset | `php artisan permission:cache-reset` |
| User tidak bisa login setelah update | Session cache lama | Hapus isi `storage/framework/sessions/` |
| Migrasi gagal di production | Tabel sudah ada / konflik | Cek `php artisan migrate:status`, selesaikan manual jika perlu |

---

## Lisensi
Sistem ini bersifat Open Source di bawah lisensi [MIT](LICENSE).
