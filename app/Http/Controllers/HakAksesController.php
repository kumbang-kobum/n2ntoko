<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HakAksesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:hakakses.lihat', only: ['index']),
            new Middleware('permission:hakakses.edit',  only: ['update', 'storeRole', 'destroyRole']),
        ];
    }

    public function index()
    {
        $roles       = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode('.', $p->name)[0]; // grup berdasarkan prefix: produk, pembelian, dll
        });

        return view('hakakses.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Jangan izinkan mengubah permission role admin
        if ($role->name === 'admin') {
            return back()->with('error', 'Permission role Admin tidak bisa diubah.');
        }

        $permissionNames = $request->input('permissions', []);
        $role->syncPermissions($permissionNames);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Permission role \"{$role->name}\" berhasil diperbarui.");
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
        ], [
            'name.unique' => 'Nama role sudah ada.',
            'name.regex'  => 'Nama role hanya boleh huruf kecil dan underscore.',
        ]);

        Role::create(['name' => $request->name]);

        return back()->with('success', "Role \"{$request->name}\" berhasil dibuat.");
    }

    public function destroyRole(Role $role)
    {
        if (in_array($role->name, ['admin', 'kasir', 'manajer', 'gudang'])) {
            return back()->with('error', 'Role bawaan sistem tidak bisa dihapus.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Role \"{$role->name}\" masih dipakai oleh {$role->users()->count()} pengguna.");
        }

        $name = $role->name;
        $role->delete();

        return back()->with('success', "Role \"{$name}\" berhasil dihapus.");
    }
}
