<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Produk & Stok
            'produk.lihat',
            'produk.tambah',
            'produk.edit',
            'produk.hapus',
            'stok.opname',
            // Pembelian
            'pembelian.lihat',
            'pembelian.tambah',
            'pembelian.edit',
            'pembelian.hapus',
            'pembelian.konfirmasi',
            // Penjualan
            'penjualan.lihat',
            'penjualan.tambah',
            'penjualan.edit',
            'penjualan.batal',
            // Pelanggan
            'pelanggan.lihat',
            'pelanggan.tambah',
            'pelanggan.edit',
            'pelanggan.hapus',
            // Beban/Pengeluaran
            'pengeluaran.lihat',
            'pengeluaran.tambah',
            'pengeluaran.edit',
            'pengeluaran.hapus',
            // Laporan
            'laporan.lihat',
            'laporan.ekspor',
            // Pegawai
            'pegawai.lihat',
            'pegawai.tambah',
            'pegawai.edit',
            'pegawai.hapus',
            // Manajemen User
            'user.lihat',
            'user.tambah',
            'user.edit',
            'user.hapus',
            // Hak Akses
            'hakakses.lihat',
            'hakakses.edit',
            // Pengaturan
            'pengaturan.lihat',
            'pengaturan.edit',
            // Absensi
            'absensi.lihat',
            'absensi.tambah',
            'absensi.rekap',
            // Activity Log
            'activity-log.lihat',
            'activity-log.hapus',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin: akses penuh
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Manajer: lihat semua + ekspor + absensi
        $manajer = Role::firstOrCreate(['name' => 'manajer']);
        $manajer->syncPermissions([
            'produk.lihat',
            'pembelian.lihat',
            'penjualan.lihat',
            'pelanggan.lihat',
            'pengeluaran.lihat',
            'pegawai.lihat',
            'laporan.lihat',
            'laporan.ekspor',
            'absensi.lihat',
            'absensi.tambah',
            'absensi.rekap',
            'activity-log.lihat',
        ]);

        // Kasir: penjualan + lihat produk/stok + pelanggan
        $kasir = Role::firstOrCreate(['name' => 'kasir']);
        $kasir->syncPermissions([
            'produk.lihat',
            'penjualan.lihat',
            'penjualan.tambah',
            'penjualan.edit',
            'penjualan.batal',
            'pelanggan.lihat',
            'pelanggan.tambah',
            'pelanggan.edit',
        ]);

        // Gudang: pembelian + produk + stok
        $gudang = Role::firstOrCreate(['name' => 'gudang']);
        $gudang->syncPermissions([
            'produk.lihat',
            'produk.tambah',
            'produk.edit',
            'stok.opname',
            'pembelian.lihat',
            'pembelian.tambah',
            'pembelian.edit',
            'pembelian.konfirmasi',
        ]);

        // Buat akun admin default
        $adminUser = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@n2ntoko.com')],
            [
                'name'     => 'Administrator',
                'password' => bcrypt(env('ADMIN_PASSWORD', 'ChangeMe_' . substr(md5(uniqid()), 0, 8))),
                'is_active'=> true,
            ]
        );
        $adminUser->assignRole('admin');
    }
}
