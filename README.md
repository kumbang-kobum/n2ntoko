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

---

## 🖥️ Panduan Instalasi aaPanel (Multi-Instance: Retail & Grosir)

Panduan ini ditujukan untuk menginstal **dua atau lebih** website N2N Toko dalam satu server aaPanel (misal: satu untuk Retail dan satu untuk Grosir) menggunakan Ubuntu 22.04.

### 🚩 Langkah 1: Persiapan Global Server (Sekali Saja)

Sebelum membuat website, pastikan software di aaPanel sudah siap:

1.  **Install Software:** Buka **App Store** dan install:
    - ✅ **Nginx** (versi terbaru)
    - ✅ **MySQL 8.0**
    - ✅ **PHP 8.2**
2.  **Konfigurasi PHP 8.2 (SANGAT PENTING):**
    - Buka **App Store** > **PHP 8.2** > **Settings**.
    - **Install Extension:** Tambahkan `fileinfo`, `bcmath`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`.
    - **Disabled functions:** Hapus (delete) fungsi berikut: `putenv`, `pcntl_signal`, `pcntl_alarm`, `proc_open`, `proc_get_status`.
    - Buka tab **Service** > Klik **Restart**.

---

### 📦 Langkah 2: Membuat Database & Website (GUI aaPanel)

Lakukan langkah ini untuk **setiap** aplikasi yang ingin diinstal.

#### A. Buat Database
Buka menu **Database** > **Add Database**:
- **Retail:** Name: `n2n_retail`, User: `user_retail`, Pass: `Rahasia123!`
- **Grosir:** Name: `n2n_grosir`, User: `user_grosir`, Pass: `Rahasia123!`

#### B. Buat Website
Buka menu **Website** > **Add Site**:
- **Retail:** Domain: `retail.n2n.com` (Root: `/www/wwwroot/retail.n2n.com`)
- **Grosir:** Domain: `grosir.n2n.com` (Root: `/www/wwwroot/grosir.n2n.com`)
- *Catatan: Jika akses via IP lokal, tambahkan port berbeda (misal: `192.168.100.5:81` dan `192.168.100.5:82`)*.

---

### 🚀 Langkah 3: Script Instalasi via Terminal

Buka **Terminal** di aaPanel. Jalankan perintah di bawah ini **per website**. Ganti bagian `FOLDER_NAME` sesuai folder website Anda.

#### 🏁 Jalankan untuk RETAIL:
```bash
# 1. Masuk ke folder retail
cd /www/wwwroot/retail.n2n.com

# 2. Bersihkan file default & Clone kode
rm -rf index.html 404.html .htaccess
git clone https://github.com/chandrair/n2ntoko.git .

# 3. Pastikan Node.js minimal v20 (SANGAT PENTING)
# Cek versi: node -v. Jika masih v12/v14, jalankan:
# npm install -g n && n 20 && hash -r

# 4. Setup Composer & Dependensi
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer install --no-dev --optimize-autoloader

# 5. Setup Node.js & Build Aset
npm install && npm run build

# 6. Setup Environment
cp .env.example .env
```

#### 🏁 Jalankan untuk GROSIR:
Ulangi perintah di atas, hanya baris pertama yang diganti:
`cd /www/wwwroot/grosir.n2n.com` ... (lanjutkan langkah 2-5).

---

### ⚙️ Langkah 4: Konfigurasi File .env (Masing-masing)

Buka **File Manager** aaPanel, cari file `.env` di masing-masing folder, dan sesuaikan isinya:

**File `.env` di /retail.n2n.com:**
```env
APP_NAME="N2N Retail"
APP_URL=http://retail.n2n.com
SESSION_COOKIE=n2n_retail_session
SESSION_DOMAIN=retail.n2n.com

DB_DATABASE=n2n_retail
DB_USERNAME=user_retail
DB_PASSWORD=Rahasia123!
```

**File `.env` di /grosir.n2n.com:**
```env
APP_NAME="N2N Grosir"
APP_URL=http://grosir.n2n.com
SESSION_COOKIE=n2n_grosir_session
SESSION_DOMAIN=grosir.n2n.com

DB_DATABASE=n2n_grosir
DB_USERNAME=user_grosir
DB_PASSWORD=Rahasia123!
```

---

### 🛠️ Langkah 5: Finalisasi & Permission (Terminal)

Kembali ke Terminal, jalankan perintah ini untuk **masing-masing folder**:

```bash
# --- Eksekusi di folder RETAIL ---
cd /www/wwwroot/retail.n2n.com
php artisan key:generate
php artisan storage:link
php artisan migrate --seed --force
php artisan optimize
chattr -i public/.user.ini
chown -R www:www . && chmod -R 775 storage bootstrap/cache
chattr +i public/.user.ini

# --- Eksekusi di folder GROSIR ---
cd /www/wwwroot/grosir.n2n.com
php artisan key:generate
php artisan storage:link
php artisan migrate --seed --force
php artisan optimize
chattr -i public/.user.ini
chown -R www:www . && chmod -R 775 storage bootstrap/cache
chattr +i public/.user.ini
```

---

### 🌐 Langkah 6: Pengaturan Akhir Website (GUI aaPanel)

Untuk **setiap website** (Retail & Grosir), klik nama website di menu **Website**:
1.  **Site directory:** Ubah **Running directory** ke `/public`. Klik Save.
2.  **URL rewrite:** Pilih template **laravel**. Klik Save.
3.  **SSL (Opsional):** Aktifkan Let's Encrypt jika domain sudah mengarah ke IP VPS.

---

## ♻️ Cara Update Aplikasi (Sangat Mudah)

Jika ada update terbaru dari GitHub, Anda hanya perlu menjalankan satu blok perintah ini di Terminal aaPanel.

**Update RETAIL:**
```bash
cd /www/wwwroot/retail.n2n.com
git fetch origin && git reset --hard origin/main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan optimize
chattr -i public/.user.ini
chown -R www:www . && chmod -R 775 storage bootstrap/cache
chattr +i public/.user.ini
```

**Update GROSIR:**
Ganti baris pertama menjadi `cd /www/wwwroot/grosir.n2n.com` lalu jalankan perintah sisanya sama seperti di atas.

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
