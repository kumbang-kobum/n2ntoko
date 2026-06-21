<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pengaturan.lihat', only: ['index']),
            new Middleware('permission:pengaturan.edit',  only: ['update']),
        ];
    }

    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'toko_nama'    => 'required|string|max:100',
            'toko_tagline' => 'nullable|string|max:150',
            'toko_alamat'  => 'nullable|string|max:300',
            'toko_kota'    => 'nullable|string|max:100',
            'toko_telepon' => 'nullable|string|max:30',
            'struk_footer' => 'nullable|string|max:200',
            'nota_header'  => 'nullable|string|max:200',
            'ppn_default'  => 'required|in:0,11,12',
            'grosir_same_price_warning' => 'required|boolean',
        ]);

        Setting::setMany($request->only([
            'toko_nama', 'toko_tagline', 'toko_alamat',
            'toko_kota', 'toko_telepon', 'struk_footer',
            'nota_header', 'ppn_default', 'grosir_same_price_warning',
        ]));

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
