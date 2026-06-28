# Catatan Perbaikan N2NToko

Hasil audit menyeluruh seluruh codebase. Setiap bug dicatat dengan lokasi, deskripsi, dan status penyelesaian.

---

## Tahap 1 — Bug Kritis ✅ SELESAI

| # | File | Baris | Masalah | Status |
|---|---|---|---|---|
| 1 | `app/Http/Controllers/StokOpnameController.php` | 86–88 | `disableLogging()`/`enableLogging()` tidak ada di Spatie — fitur stok opname crash 100% setiap disimpan. Diganti `saveQuietly()`. | ✅ Selesai |
| 2 | `app/Http/Controllers/UserController.php` | 62, 87 | Password di-hash dua kali: `bcrypt()` di controller + cast `hashed` di model → user baru tidak bisa login. Hapus `bcrypt()`. | ✅ Selesai |
| 3 | `routes/auth.php` | 15–18 | Route `/register` terbuka publik — siapapun bisa buat akun dan langsung login tanpa persetujuan admin. Route dihapus. | ✅ Selesai |
| 4 | `app/Http/Controllers/SaleController.php` | 443–476 | `destroy()` tanpa `lockForUpdate` — dua request batal bersamaan bisa pulihkan stok 2x lipat. Ditambah lock + transaksi. | ✅ Selesai |
| 5 | `app/Http/Controllers/PurchaseController.php` | 188–200 | `confirm()` cek status di luar transaksi — double-confirm bisa tambah stok & hitung avg_cost dua kali. Lock ditambah di `confirmPurchase()`. | ✅ Selesai |
| 6 | `app/Http/Controllers/PurchaseController.php` | 203–228 | `pay()` tanpa transaksi & lock — dua pembayaran bersamaan bisa hasilkan paid_amount salah. Dibungkus transaksi + lock. | ✅ Selesai |
| 7 | `app/Http/Controllers/PurchaseController.php` | 231–243 | `destroy()` hapus items & purchase tanpa transaksi — jika salah satu gagal data inkonsisten. Dibungkus `DB::transaction()`. | ✅ Selesai |

---

## Tahap 2 — Bug Medium ✅ SELESAI

| # | File | Baris | Masalah | Status |
|---|---|---|---|---|
| 8 | `app/Http/Controllers/PurchaseController.php` | 339 | `saveItems()` tidak validasi `unit->conversion > 0` — division by zero jika data konversi 0. Ditambah cek `conversion <= 0`. | ✅ Selesai |
| 9 | `app/Http/Controllers/PurchaseController.php` | 314–315 | Validasi `unit_id` hanya cek unit ada, tidak verifikasi unit milik produk yang dipilih. Ditambah cek `unit->product_id`. | ✅ Selesai |
| 10 | `app/Http/Controllers/ProductController.php` | 145–163 | `destroy()` soft-delete product tapi hard-delete relasi anak — data rusak permanen jika product di-restore. Diganti `forceDelete()`. | ✅ Selesai |
| 11 | `app/Http/Controllers/ProductController.php` | 145–163 | `destroy()` tidak dibungkus transaksi — jika salah satu langkah gagal, data setengah terhapus. Dibungkus `DB::transaction()`. | ✅ Selesai |
| 12 | `app/Http/Controllers/LaporanController.php` | 173–174 | `shift()`: pengeluaran tidak difilter per kasir — laba bersih per kasir salah. Pengeluaran hanya dikurangi saat tampil semua kasir. | ✅ Selesai |
| 13 | `app/Http/Controllers/AttendanceController.php` | 49–63 | `checkIn()` tidak atomic — dua request check-in bersamaan bisa hasilkan double record. Dibungkus transaksi + `lockForUpdate`. | ✅ Selesai |
| 14 | `app/Http/Controllers/ProductController.php` | 215 | `base_unit_index` out of bounds tidak dihandle — `base_unit_id` jadi null, konversi rusak. Ditambah guard di `syncUnits()`. | ✅ Selesai |
| 15 | `database/migrations/..._add_ordered_status_to_purchases.php` | 10, 15 | `DB::statement ALTER TABLE ENUM` syntax MySQL-only — gagal di SQLite/testing environment. | ⏭️ Skip (migration sudah berjalan di prod, risiko lebih besar jika diubah) |
| 16 | `app/Models/Attendance.php` | 16 | `check_in`/`check_out` tidak di-cast padahal kolom bertipe `time`. Ditambah cast `datetime:H:i`. | ✅ Selesai |
| 17 | `database/migrations/..._create_employees_table.php` | 14 | Kolom `address` pakai `string(255)`. Migration baru `2026_06_28_000001_fix_employees_address_column.php` mengubah ke `text`. | ✅ Selesai |
| 18 | `app/Models/Attendance.php` & `app/Traits/HasActivityLog.php` | 33 | `getActivitySubjectName()` selalu kosong untuk Attendance. Override di `Attendance` model. | ✅ Selesai |
| — | `app/Models/Employee.php` | — | Relasi `hasMany(Attendance::class)` tidak ada. Ditambahkan. | ✅ Selesai |

---

## Tahap 3 — Bug Minor ✅ SELESAI

| # | File | Baris | Masalah | Status |
|---|---|---|---|---|
| 19 | `app/Http/Controllers/SupplierController.php` | 71–72 | Dua `update()` terpisah → 2 query + 2 activity log entry. Digabung dengan `array_merge`. | ✅ Selesai |
| 20 | `app/Http/Controllers/ExpenseController.php` | 50, 77 | Kategori hardcoded di validasi. Diganti `Rule::in(array_keys(Expense::categoryLabels()))`. | ✅ Selesai |
| 21 | `app/Http/Controllers/HakAksesController.php` | 66–67 | `$role->users()->count()` dipanggil 2x. Disimpan ke variabel `$userCount`. | ✅ Selesai |
| 22 | `app/Http/Controllers/ActivityLogController.php` | 94–95 | Filter `stok_opname` tidak konsisten antara `index()` dan `destroyAll()`. `destroyAll()` diseragamkan menggunakan `log_name`. | ✅ Selesai |
| 23 | `app/Http/Controllers/CustomerController.php` | 87 | `sales()->exists()` tidak exclude transaksi batal. Ditambah `->where('status', '!=', 'cancelled')`. | ✅ Selesai |
| 24 | `app/Models/Employee.php` | — | Tidak ada relasi `hasMany(Attendance::class)`. Sudah ditambahkan di Tahap 2. | ✅ Selesai (Tahap 2) |
| 25 | `app/Models/Setting.php` | 38 | Override `all()` tidak standar Eloquent. Ditambah komentar penjelasan + method `allAsArray()` baru. `all()` tetap ada karena 3 tempat bergantung pada perilakunya. | ✅ Selesai |
| 26 | `app/Models/Product.php` | 64–68 | `generateSku()` race condition. Ditambah `lockForUpdate()` — efektif saat dipanggil dalam transaksi (di store/update). | ✅ Selesai |
| 27 | `database/migrations/` | — | Duplikat index pada `product_barcodes.barcode`. Migration baru `2026_06_28_000002_drop_duplicate_barcode_index.php` menghapus index reguler yang redundan. | ✅ Selesai |
| 28 | `routes/web.php` | 38 | Dashboard tanpa permission spesifik. Risiko sudah ditutup dengan menghapus route `/register` (Tahap 1 #3). Semua role yang login memerlukan akses dashboard. | ✅ Accepted (by design) |
| 29 | `app/Http/Controllers/EmployeeController.php` | 98–103 | `destroy()` tidak cek attendance records. Ditambah cek `attendances()->exists()` dengan pesan informatif. | ✅ Selesai |

---

## Ringkasan Progress

| Tahap | Total Bug | Selesai | Sisa |
|---|---|---|---|
| Kritis | 7 | 7 ✅ | 0 |
| Medium | 11 | 10 ✅ | 1 (skip) |
| Minor | 11 | 11 ✅ | 0 |
| **Total** | **29** | **28** | **1 (skip)** |

*Terakhir diperbarui: 2026-06-28 — Semua tahap selesai.*
