# N2N Toko — Sistem Manajemen Grosir & Gudang

Aplikasi POS (Point of Sale) berbasis web untuk mengelola pembelian, penjualan, stok, dan laporan laba rugi. Mendukung multi-satuan produk, harga grosir & eceran, barcode scanner, PPN, metode pembayaran, dan manajemen hak akses per peran.

**Stack:** Laravel 12 · MySQL · Tailwind CSS · Alpine.js · Spatie Permission · Spatie ActivityLog

---

## Fitur Utama

| Modul | Fitur |
|---|---|
| **Produk** | Multi-satuan (Pcs/Slop/Box/dll), barcode per satuan, harga eceran & grosir, auto-switch harga grosir otomatis |
| **Pembelian** | Draft → Dipesan → Diterima, HPP average cost otomatis, bayar hutang cicil |
| **Penjualan** | Barcode scanner, auto-harga grosir berdasarkan qty, PPN 0/11/12%, Cash/Debit/QRIS, cetak struk 80mm otomatis |
| **Pelanggan** | CRUD, tipe Umum/Grosir/Langganan |
| **Supplier** | CRUD, riwayat pembelian |
| **Stok Opname** | Penyesuaian stok fisik vs sistem |
| **Pengeluaran** | Catat beban operasional (sewa, listrik, gaji, dll) |
| **Laporan Keuangan** | Laba kotor, laba bersih, PPN, top produk, breakdown metode bayar per periode |
| **Laporan Per Kasir** | Rekap harian per kasir, cetak A4 |
| **Pegawai** | CRUD data karyawan, jabatan, tanggal bergabung |
| **Absensi** | Absensi mandiri via QR/link publik (tanpa login), rekap bulanan, ekspor CSV |
| **Pengguna** | CRUD user, assign role, aktif/nonaktif |
| **Hak Akses** | Kelola role & permission kustom via UI |
| **Pengaturan Toko** | Nama toko, alamat, nomor telepon, catatan struk |
| **Riwayat Aktivitas** | Audit trail otomatis — catat semua tambah/edit/hapus beserta nama pengguna & nilai sebelum–sesudah |

---

## Instalasi

Pilih metode sesuai lingkungan Anda:

| Metode | Cocok untuk |
|---|---|
| [aaPanel (VPS)](#-instalasi-via-aapanel-vps--production) | Server production, VPS Ubuntu/CentOS |
| [XAMPP (Windows)](#-instalasi-via-xampp-windows--lokal) | Development lokal di Windows |
| [Git + CLI (Linux/Mac)](#-instalasi-via-git--cli-linuxmac) | Developer, server manual |

---

## 🖥️ Instalasi via aaPanel (VPS) — Production

**Rekomendasi:** Ubuntu 22.04 LTS, minimal 1 CPU, 1 GB RAM, 20 GB disk.

### 1️⃣ Persiapan Awal di aaPanel

1. Masuk ke menu **App Store** (Software Store), pastikan Anda sudah menginstall:
   - ✅ **Nginx** (versi terbaru)
   - ✅ **MySQL 8.0**
   - ✅ **PHP 8.2**
2. Buka menu **App Store** > **PHP 8.2** > **Settings**:
   - **Install Extension:** Pastikan `fileinfo`, `bcmath`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, dan `ctype` terinstall.
   - **Disabled functions (SANGAT PENTING):** Hapus fungsi-fungsi berikut agar proses instalasi dan build tidak error: `putenv`, `pcntl_signal`, `pcntl_alarm`, `proc_open`, `proc_get_status`.
   - Buka tab **Service** lalu klik **Restart**.

### 2️⃣ Buat Database & Website

1. Buka menu **Database** > **MySQL** > **Add Database**:
   - Database Name: `n2ntoko`
   - Username: `n2ntoko_user` (atau sesuai keinginan)
   - Password: buat password yang kuat dan catat.
2. Buka menu **Website** > **Add Site**:
   - **Domain:** Isi dengan IP VPS atau Domain Anda (misal: `192.168.100.5` atau `toko.domainanda.com`).
   - **PHP Version:** Pilih **8.2**.
   - Klik **Submit**.

### 3️⃣ Deploy Aplikasi via Terminal aaPanel

Buka **Terminal** di aaPanel (pastikan Anda login sebagai `root`), lalu jalankan perintah berikut secara berurutan. Salin-tempel (copy-paste) per blok:

```bash
# 1. Masuk ke direktori website (Ganti 192.168.100.5 dengan Domain/IP website Anda)
cd /www/wwwroot/192.168.100.5

# 2. Hapus file default bawaan aaPanel (WAJIB)
rm -rf index.html 404.html .htaccess

# 3. Clone repositori (PENTING: Pastikan ada spasi dan TITIK (.) di akhir perintah!)
git clone https://github.com/chandrair/n2ntoko.git .

# 4. Install Composer (jika server belum memilikinya)
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 5. Install dependensi PHP
composer install --no-dev --optimize-autoloader

# 6. Salin file environment
cp .env.example .env
```

### 4️⃣ Konfigurasi .env & Build Aset

1. Kembali ke **aaPanel File Manager**, buka folder website Anda (`/www/wwwroot/192.168.100.5`).
2. Cari file `.env` (pastikan pengaturan "Show hidden files" aktif), edit dan sesuaikan bagian ini:

```env
APP_NAME="N2N Toko"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://192.168.100.5

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=n2ntoko
DB_USERNAME=n2ntoko_user
DB_PASSWORD=password_database_yang_dibuat_tadi

ADMIN_EMAIL=admin@toko.com
ADMIN_PASSWORD=PasswordKuat123!
```
3. Simpan file `.env`.
4. Kembali ke **Terminal**, jalankan sisa perintah berikut:

```bash
# 1. Install Node.js v20 (jika belum ada)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# 2. Install dependensi Node & Build aset Tailwind/Vite
npm install
npm run build

# 3. Setup Laravel (Key, Storage, Database, Seed)
php artisan key:generate
php artisan storage:link
php artisan migrate --seed

# 4. Optimasi Cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Perbaiki Izin Folder (SANGAT PENTING agar web tidak error 500/Permission Denied)
chown -R www:www /www/wwwroot/192.168.100.5
chmod -R 775 storage bootstrap/cache
```
*(Ingat: Ubah `/www/wwwroot/192.168.100.5` dengan direktori asli Anda jika menggunakan domain)*

### 5️⃣ Konfigurasi Website Akhir (GUI aaPanel)

Kembali ke menu **Website** di aaPanel, klik pada nama Domain/IP Anda:
1. **Site directory:**
   - Site directory: `/www/wwwroot/192.168.100.5`
   - Running directory: Pilih `/public`
   - Klik **Save**.
2. **URL rewrite:**
   - Pilih template **laravel** dari dropdown.
   - Klik **Save**.
3. Buka browser dan akses alamat web Anda (misal: `http://192.168.100.5`). Login dengan email dan password yang Anda isi di file `.env`.

---

## 🏗️ Setup Multi-Instance (Dua Aplikasi dalam Satu Server)

Jika Anda ingin menginstall dua aplikasi (misal: satu untuk **Retail** dan satu untuk **Grosir**) dengan database yang berbeda di satu server aaPanel, ikuti panduan ini:

### 1. Buat Dua Database Berbeda
Di menu **Database**, buat dua database:
- `db_retail` (User: `user_retail`)
- `db_grosir` (User: `user_grosir`)

### 2. Buat Dua Website Berbeda
Di menu **Website** > **Add Site**, buat dua entri:

| No | Domain / Hostname | Root Directory |
|---|---|---|
| **1** | `retail.n2n.com` (atau `192.168.100.5:81`) | `/www/wwwroot/retail.n2n.com` |
| **2** | `grosir.n2n.com` (atau `192.168.100.5:82`) | `/www/wwwroot/grosir.n2n.com` |

> **Tips Jika Menggunakan IP (Lokal):** Jika Anda tidak menggunakan domain, Anda bisa menggunakan nomor port yang berbeda (misal `:81` dan `:82`) saat menambahkan site di aaPanel.

### 3. Deploy ke Masing-Masing Folder
Anda harus menjalankan proses **Langkah 3 & 4** pada bagian Instalasi di atas untuk **setiap folder**.

**Terminal untuk Retail:**
```bash
cd /www/wwwroot/retail.n2n.com
# ... jalankan git clone . , composer install, npm install, dll ...
```

**Terminal untuk Grosir:**
```bash
cd /www/wwwroot/grosir.n2n.com
# ... jalankan git clone . , composer install, npm install, dll ...
```

### 4. Konfigurasi .env yang Berbeda
PENTING: Edit file `.env` di masing-masing folder melalui File Manager:
- Di folder **retail**, hubungkan ke `DB_DATABASE=db_retail`
- Di folder **grosir**, hubungkan ke `DB_DATABASE=db_grosir`

### 5. Cron Job Terpisah
Tambahkan dua Cron Job di menu **Cron** aaPanel:
- Task 1: `php /www/wwwroot/retail.n2n.com/artisan schedule:run >> /dev/null 2>&1`
- Task 2: `php /www/wwwroot/grosir.n2n.com/artisan schedule:run >> /dev/null 2>&1`

---

## ♻️ Update Aplikasi (aaPanel)

Untuk memastikan pembaruan aplikasi berjalan lancar, aman, dan struktur folder tidak rusak, jalankan script ini di **Terminal aaPanel**.

*(Ganti `192.168.100.5` dengan direktori website Anda yang sebenarnya)*

```bash
# 1. Masuk ke direktori web
cd /www/wwwroot/192.168.100.5

# 2. Tarik kode terbaru dari GitHub (Paksa timpa jika ada perubahan lokal)
git fetch origin
git reset --hard origin/main

# 3. Update dependensi PHP & JS
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 4. Migrasi Database & Bersihkan Cache
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan optimize:clear
php artisan optimize

# 5. Fix Permission Ulang (Menghindari file hasil pull tidak bisa dibaca Nginx)
chown -R www:www .
chmod -R 775 storage bootstrap/cache
```

---

## 🪟 Instalasi via XAMPP (Windows) — Lokal

### Prasyarat

- **XAMPP** dengan PHP 8.2+ — download di [apachefriends.org](https://www.apachefriends.org)
- **Composer** — download di [getcomposer.org](https://getcomposer.org)
- **Node.js 20+** — download di [nodejs.org](https://nodejs.org)
- **Git** — download di [git-scm.com](https://git-scm.com)

### Langkah 1 — Jalankan XAMPP

Buka **XAMPP Control Panel**, klik **Start** pada:
- ✅ **Apache**
- ✅ **MySQL**

### Langkah 2 — Buat Database

Buka browser → `http://localhost/phpmyadmin`
- Klik **New** di sidebar kiri
- Database name: `n2ntoko`
- Collation: `utf8mb4_unicode_ci`
- Klik **Create**

### Langkah 3 — Clone / Download Repositori

Buka **Git Bash** atau **Command Prompt**, lalu:

```bash
# Masuk ke folder htdocs XAMPP
cd C:\xampp\htdocs

# Clone repositori
git clone https://github.com/USERNAME/n2ntoko.git
cd n2ntoko
```

Atau download ZIP dari GitHub → Extract ke `C:\xampp\htdocs\n2ntoko\`

### Langkah 4 — Install Dependensi

Buka **Command Prompt** atau **Git Bash** di folder `n2ntoko`:

```bash
# Install dependensi PHP
composer install

# Install dependensi JS
npm install

# Build aset
npm run build
```

### Langkah 5 — Konfigurasi .env

```bash
# Salin file environment
copy .env.example .env
```

Edit file `.env` dengan Notepad atau VS Code:

```env
APP_NAME="N2N Toko"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/n2ntoko/public
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=n2ntoko
DB_USERNAME=root
DB_PASSWORD=
# Catatan: password MySQL XAMPP default kosong

ADMIN_EMAIL=admin@n2ntoko.com
ADMIN_PASSWORD=Admin123!
```

### Langkah 6 — Setup Aplikasi

```bash
# Generate app key
php artisan key:generate

# Migrasi database & seeder
php artisan migrate --seed
```

### Langkah 7 — Buka Aplikasi

Buka browser: `http://localhost/n2ntoko/public`

Login dengan `ADMIN_EMAIL` dan `ADMIN_PASSWORD` dari `.env`.

> **Tips:** Untuk URL yang lebih rapi tanpa `/n2ntoko/public`, buat Virtual Host di XAMPP:
> Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, tambahkan:
> ```apache
> <VirtualHost *:80>
>     DocumentRoot "C:/xampp/htdocs/n2ntoko/public"
>     ServerName n2ntoko.test
>     <Directory "C:/xampp/htdocs/n2ntoko/public">
>         AllowOverride All
>         Require all granted
>     </Directory>
> </VirtualHost>
> ```
> Tambahkan `127.0.0.1 n2ntoko.test` di file `C:\Windows\System32\drivers\etc\hosts`.
> Restart Apache, buka `http://n2ntoko.test`.

---

## 🐧 Instalasi via Git + CLI (Linux/Mac)

### Prasyarat

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y php8.2 php8.2-{fpm,mbstring,xml,bcmath,curl,mysql,zip,gd,fileinfo} \
     mysql-server nginx nodejs npm git unzip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# macOS (Homebrew)
brew install php@8.2 mysql node composer git
```

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone https://github.com/USERNAME/n2ntoko.git
cd n2ntoko

# 2. Install dependensi
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Setup environment
cp .env.example .env
nano .env   # edit konfigurasi database dan app
```

Isi `.env`:

```env
APP_NAME="N2N Toko"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://domain-atau-ip-anda
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=n2ntoko
DB_USERNAME=n2ntoko_user
DB_PASSWORD=password_kuat_anda

ADMIN_EMAIL=admin@tokoku.com
ADMIN_PASSWORD=PasswordKuatAnda123!
```

```bash
# 4. Buat database MySQL
mysql -u root -p -e "
  CREATE DATABASE n2ntoko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'n2ntoko_user'@'localhost' IDENTIFIED BY 'password_kuat_anda';
  GRANT ALL PRIVILEGES ON n2ntoko.* TO 'n2ntoko_user'@'localhost';
  FLUSH PRIVILEGES;
"

# 5. Setup aplikasi
php artisan key:generate
php artisan migrate --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Permission storage
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data .   # Ubuntu (ganti dengan user Nginx/Apache)

# 7. Jalankan (development)
php artisan serve
```

Konfigurasi Nginx (production):

```nginx
server {
    listen 80;
    server_name domain-atau-ip-anda;
    root /path/ke/n2ntoko/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Development (Local)

```bash
# Terminal 1 — Backend
php artisan serve

# Terminal 2 — Frontend (hot reload)
npm run dev
```

Buka `http://localhost:8000`

---

## Panduan Pemakaian

### Urutan Setup Awal (lakukan sekali saat pertama kali)

```
1. Login sebagai Admin
2. Buat Kategori produk
3. Daftarkan Supplier
4. Tambah Produk beserta satuan & harga
5. Buat akun untuk kasir dan petugas gudang
6. Lakukan Pembelian pertama untuk mengisi stok
```

---

### 1. Kelola Kategori

**Inventaris → Kategori**

- Klik kolom input di atas → ketik nama kategori → klik **+ Tambah**
- Contoh: `Rokok`, `Minuman`, `Snack`, `Sembako`, `Kebersihan`
- Edit nama: klik ikon pensil di kanan baris
- Hapus: klik ikon tempat sampah (hanya bisa jika tidak ada produk)

---

### 2. Kelola Supplier

**Inventaris → Supplier**

- Isi form di sebelah kiri: Nama, Telepon, Kontak Person, Alamat → klik **Tambah Supplier**
- Edit: klik tombol **Edit** di baris supplier → ubah data → klik **Simpan**
- Nonaktifkan supplier dengan uncheck kotak **Aktif** saat edit

---

### 3. Tambah Produk dengan Multi-Satuan

**Inventaris → Produk → Tambah Produk**

#### Informasi Produk
- Isi **Nama Produk** (wajib)
- SKU otomatis terisi, bisa diubah manual
- Pilih **Kategori**
- Isi **Stok Minimum** — notifikasi muncul di dashboard jika stok di bawah angka ini

#### Setup Satuan (bagian terpenting)

Contoh produk **Rokok Sampoerna Mild 16**:

| Baris | Satuan | Konversi | Barcode | Harga Eceran | Harga Grosir | Min Qty Grosir |
|---|---|---|---|---|---|---|
| 1 *(radio dipilih = Dasar)* | Pcs | 1 | 8999999100016 | 28.000 | 26.500 | 10 |
| 2 | Slop | 10 | 8999999100023 | 270.000 | 260.000 | 1 |
| 3 | Box | 500 | — | 13.000.000 | 12.500.000 | 1 |

**Penjelasan kolom:**
- **Radio di kiri** → klik untuk jadikan **Satuan Dasar** (stok dihitung dalam satuan ini)
- **Konversi** → berapa kali satuan dasar. Pcs=1, Slop=10 Pcs, Box=500 Pcs
- **Min Qty Grosir** → jika kasir input qty ≥ angka ini, harga otomatis pindah ke harga grosir
- **+ Tambah Satuan** → klik untuk tambah baris satuan baru

> **Aturan:** Konversi selalu ke satuan dasar. Stok tersimpan dalam satuan dasar.

---

### 4. Kelola Pelanggan

**Penjualan → Pelanggan**

- Isi form di kiri: Nama, Tipe (Umum/Grosir/Langganan), Telepon, Alamat → **Tambah Pelanggan**
- Tipe berguna untuk filter dan identifikasi pelanggan saat kasir
- Edit inline: klik **Edit** di baris pelanggan

---

### 5. Alur Pembelian (Stok Masuk dari Supplier)

**Inventaris → Pembelian → Tambah Pembelian**

#### Status Pembelian

```
Draft  →  Dipesan  →  Diterima (stok masuk)
  ↑            ↑
(bisa edit)  (hanya ubah status)
```

| Status | Stok | Bisa Edit? |
|---|---|---|
| **Draft** | Belum berubah | ✅ Bisa diedit penuh |
| **Dipesan** | Belum berubah | ❌ Hanya ubah status |
| **Diterima** | ✅ Masuk ke stok | ❌ Tidak bisa diubah |

#### Cara Input Pembelian

1. Pilih **Supplier** (opsional)
2. Isi **Tanggal** dan **No. Faktur**
3. Ketik nama produk di kolom pencarian → pilih dari dropdown
4. Pilih **Satuan** (misal: Box) → isi **Qty** → isi **Harga Beli** per satuan
5. Tambah baris item lain jika perlu
6. Pilih aksi:
   - **Simpan Draft** → belum masuk stok, bisa edit kembali
   - **Konfirmasi & Simpan** → stok langsung masuk, HPP diperbarui

#### Bayar Hutang Supplier

Jika pembelian belum lunas (harga beli belum dibayar penuh):
- Buka detail pembelian → lihat panel oranye **Sisa Hutang**
- Klik **Catat Pembayaran** → isi jumlah bayar → **Simpan**
- Bisa dicicil beberapa kali sampai status berubah **Lunas**

---

### 6. Alur Penjualan (Kasir)

**Penjualan → Transaksi Baru**

#### Cara Transaksi

**A. Cari produk manual:**
- Ketik nama atau SKU di kolom **Cari produk** → pilih dari dropdown

**B. Scan barcode:**
- Klik kolom **Scan barcode** → arahkan scanner → produk otomatis masuk keranjang

#### Mengatur Item di Keranjang

| Kolom | Cara Pakai |
|---|---|
| **Satuan** | Klik dropdown → pilih Pcs / Slop / Box |
| **Qty** | Ketik jumlah — harga otomatis pindah ke grosir jika qty ≥ batas |
| **Harga** | Bisa diubah manual jika perlu |
| **★ Auto Grosir** | Badge ungu muncul jika qty memenuhi syarat grosir |

#### Opsi Transaksi (panel kanan)

| Opsi | Pilihan |
|---|---|
| **Tipe Harga** | Eceran / Grosir (berlaku untuk semua item) |
| **PPN** | Non-PPN / 11% / 12% |
| **Metode Bayar** | Tunai / Debit / QRIS |

**Untuk pembayaran Tunai:**
- Isi **Uang Diterima** → kembalian otomatis dihitung
- Gunakan tombol cepat (Rp 10.000 / 50.000 / 100.000)

**Untuk Debit/QRIS:**
- Kolom uang diterima disembunyikan — total langsung dianggap lunas

Klik **✓ Proses Pembayaran** → struk 80mm otomatis terbuka untuk dicetak.

#### Membatalkan Transaksi

- Buka detail penjualan → klik **Batalkan Transaksi**
- Stok otomatis dikembalikan ke gudang

---

### 7. Stok Opname

**Inventaris → Stok Opname**

Digunakan saat stok fisik berbeda dengan sistem (susut, rusak, hilang).

1. Hitung stok fisik di gudang
2. Isi angka stok fisik di kolom **Stok Fisik** untuk produk yang berbeda
3. Produk yang sama dengan sistem bisa dikosongkan (tidak akan diproses)
4. Klik **Simpan Penyesuaian**
5. Selisih otomatis dicatat di kartu stok sebagai `adjustment`

---

### 8. Catat Pengeluaran

**Pengeluaran → Catat Pengeluaran**

1. Klik tombol **Catat Pengeluaran** (pojok kanan atas)
2. Isi:
   - **Tanggal** — tanggal pengeluaran terjadi
   - **Kategori** — Sewa / Listrik / Air / Gaji / Transport / Pemeliharaan / Lainnya
   - **Keterangan** — deskripsi singkat (misal: "Bayar listrik Mei")
   - **Jumlah** — nominal dalam Rupiah
   - **No. Bukti** — nomor kwitansi/nota (opsional)
3. Klik **Simpan**

Pengeluaran akan masuk ke perhitungan **Laba Bersih** di laporan.

---

### 9. Melihat Laporan

#### Laporan Keuangan
**Laporan → Laporan Keuangan**

Pilih periode: **Hari Ini / Minggu Ini / Bulan Ini / Bulan Lalu / Tahun Ini / Kustom**

| Kartu | Isi |
|---|---|
| Total Penjualan | Omzet kotor (termasuk PPN) |
| PPN Terpungut | Total PPN dari semua transaksi |
| Laba Kotor | Pendapatan bersih − HPP |
| Laba Bersih | Laba Kotor − Total Pengeluaran |

Bagian bawah menampilkan:
- Tabel penjualan harian dengan laba per hari
- Top 10 produk terlaris
- Breakdown pengeluaran per kategori
- Ringkasan Laba Rugi lengkap

#### Laporan Per Kasir
**Laporan → Laporan Per Kasir**

1. Pilih **Tanggal**
2. Pilih **Kasir** (atau biarkan "Semua Kasir")
3. Klik **Tampilkan**

Menampilkan setiap kasir dalam blok terpisah:
- Jumlah transaksi
- Rincian per metode bayar (Tunai / Debit / QRIS)
- Total penjualan dan PPN

**Cetak A4:** Klik tombol **Cetak A4** → laporan terformat untuk printer biasa (Epson LX310/laser).

---

### 10. Kelola Pengguna

**Pengguna** *(hanya Admin)*

#### Tambah Pengguna Baru
1. Klik **Tambah Pengguna**
2. Isi Nama, Email, Password
3. Pilih **Role**: Admin / Manajer / Kasir / Gudang
4. Aktifkan toggle **Aktif**
5. Klik **Simpan**

#### Nonaktifkan Pengguna
Edit pengguna → matikan toggle **Aktif** → Simpan.
Pengguna tidak bisa login tetapi data transaksinya tetap tersimpan.

---

## Hak Akses Per Role

### Deskripsi Role

| Role | Untuk Siapa | Akses |
|---|---|---|
| **Admin** | Pemilik / Pengelola | Semua fitur tanpa batasan |
| **Manajer** | Kepala Toko / Supervisor | Lihat semua data & laporan, tidak bisa ubah |
| **Kasir** | Petugas Kasir | Transaksi penjualan, kelola pelanggan |
| **Gudang** | Petugas Gudang | Kelola produk, input & konfirmasi pembelian |

### Matrix Permission Lengkap

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
| pegawai.lihat | ✅ | ✅ | — | — |
| pegawai.tambah | ✅ | — | — | — |
| pegawai.edit | ✅ | — | — | — |
| pegawai.hapus | ✅ | — | — | — |
| absensi.lihat | ✅ | ✅ | — | — |
| absensi.tambah | ✅ | ✅ | — | — |
| absensi.rekap | ✅ | ✅ | — | — |
| user.lihat | ✅ | — | — | — |
| user.tambah | ✅ | — | — | — |
| user.edit | ✅ | — | — | — |
| user.hapus | ✅ | — | — | — |
| hakakses.lihat | ✅ | — | — | — |
| hakakses.edit | ✅ | — | — | — |
| pengaturan.lihat | ✅ | — | — | — |
| pengaturan.edit | ✅ | — | — | — |
| activity-log.lihat | ✅ | ✅ | — | — |
| activity-log.hapus | ✅ | — | — | — |

### 11. Riwayat Aktivitas (Audit Trail)

**Admin → Riwayat Aktivitas** *(Admin & Manajer)*

Semua perubahan data dicatat otomatis: tambah, edit, dan hapus pada seluruh modul (Produk, Penjualan, Pembelian, Pelanggan, Supplier, Karyawan, Pengeluaran, Absensi, Harga, Pengaturan, Pengguna).

Setiap entri log mencatat:
- **Waktu** — tanggal dan jam persis perubahan terjadi
- **Pengguna** — nama lengkap yang melakukan aksi
- **Aksi** — Tambah / Edit / Hapus
- **Modul** — modul mana yang diubah
- **ID Data** — ID record yang terdampak
- **Perubahan** — nilai sebelum dan sesudah (khusus Edit)

#### Filter Log
Gunakan filter di bagian atas untuk mempersempit pencarian:
- Nama pengguna (pencarian sebagian)
- Modul (Produk, Penjualan, dll)
- Aksi (Tambah / Edit / Hapus)
- Rentang tanggal (Dari — Sampai)

#### Hapus Log *(khusus Admin)*
- **Hapus per baris** — klik ikon sampah di baris log yang ingin dihapus
- **Hapus semua (filter aktif)** — tombol di atas tabel, menghapus semua log sesuai filter yang sedang aktif; berguna untuk membersihkan log lama per periode atau per modul tertentu

> **Perhatian:** Penghapusan log tidak bisa dibatalkan. Gunakan dengan hati-hati.

---

### Menambah Role / Permission Kustom

Edit `database/seeders/RolePermissionSeeder.php` lalu jalankan:

```bash
php artisan db:seed --class=RolePermissionSeeder
php artisan permission:cache-reset
```

---

## Konsep Penting

### HPP Average Cost (Rata-rata Bergerak)

Setiap pembelian dikonfirmasi, HPP produk dihitung ulang:

```
HPP Baru = (Stok Lama × HPP Lama) + (Qty Masuk × Harga Beli per Dasar)
           ────────────────────────────────────────────────────────────────
                              Stok Lama + Qty Masuk
```

HPP ini yang digunakan untuk menghitung profit di setiap transaksi penjualan.

### Auto-Switch Harga Grosir

Sistem otomatis pindah ke harga grosir berdasarkan **Min Qty Grosir** per satuan:

```
Kasir input 12 Pcs  →  Min Qty Grosir = 10  →  Harga Grosir aktif (★ Auto Grosir)
Kasir input  8 Pcs  →  Min Qty Grosir = 10  →  Harga Eceran tetap digunakan
```

Setting **Min Qty Grosir** dikonfigurasi per satuan saat tambah/edit produk.

### Kartu Stok (Stock Ledger)

Setiap pergerakan stok tercatat otomatis:

| Tipe | Sumber |
|---|---|
| `in` | Pembelian dikonfirmasi |
| `out` | Penjualan |
| `adjustment` | Stok opname atau pembatalan penjualan |

### PPN (Pajak Pertambahan Nilai)

- Dipilih per transaksi: **Non-PPN / 11% / 12%**
- `total_amount` = subtotal + PPN
- Laporan memisahkan subtotal, PPN, dan total secara terpisah
- Struk mencantumkan PPN jika berlaku

### Format Cetak

| Format | Untuk | Ukuran |
|---|---|---|
| **Struk Thermal** | Nota penjualan, otomatis muncul setelah bayar | 80mm × auto |
| **Laporan A4** | Laporan per kasir, tombol "Cetak A4" | A4 portrait |

---

## Struktur Navigasi

```
/                  → Welcome (belum login) | Dashboard (sudah login)
/dashboard         → Ringkasan omzet, laba, produk, stok menipis
/products          → Daftar & kelola produk
/categories        → Kategori produk
/stok-opname       → Penyesuaian stok fisik
/suppliers         → Data supplier
/purchases         → Pembelian dari supplier
/sales             → Transaksi penjualan (kasir)
/customers         → Data pelanggan
/expenses          → Pengeluaran operasional
/laporan           → Laporan keuangan per periode
/laporan/shift     → Laporan per kasir harian
/employees         → Data karyawan
/absensi           → Absensi mandiri (publik, tanpa login)
/absensi/harian    → Input absensi harian (admin)
/absensi/rekap     → Rekap bulanan per karyawan
/users             → Manajemen pengguna & role
/hak-akses         → Kelola role & permission
/pengaturan        → Pengaturan nama toko, alamat, struk
/activity-log      → Riwayat semua aktivitas (audit trail)
```

---

## Kesesuaian Jenis Usaha

| Jenis Usaha | Dukungan | Catatan |
|---|---|---|
| Toko Grosir | ✅ Penuh | Harga grosir/eceran, multi-satuan, pelanggan tipe grosir |
| Distributor / Gudang | ✅ Penuh | Beli per Box/Karton, jual per Slop/Pcs ke warung |
| Toko Retail / Warung | ✅ Penuh | Pakai satuan eceran saja |
| Toko Campuran | ✅ Penuh | Pilih tipe harga per transaksi |
| Mini Market | ✅ Baik | Tambah banyak kategori & produk |
| Apotek / Toko Bahan | ✅ Baik | Gunakan satuan berat (Kg, Gram, Liter) |

---

## Roadmap

| Fitur | Status |
|---|---|
| Piutang pelanggan (jual kredit) | Belum ada |
| Ekspor laporan ke Excel/PDF | Belum ada |
| Pengaturan toko (nama, alamat, catatan struk) | ✅ Tersedia |
| Audit trail / riwayat aktivitas | ✅ Tersedia |
| Multi-cabang / multi-gudang | Belum ada |
