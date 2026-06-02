<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default settings
        $defaults = [
            ['key' => 'toko_nama',      'value' => 'N2N Toko'],
            ['key' => 'toko_tagline',   'value' => 'Sistem Manajemen Grosir'],
            ['key' => 'toko_alamat',    'value' => ''],
            ['key' => 'toko_kota',      'value' => ''],
            ['key' => 'toko_telepon',   'value' => ''],
            ['key' => 'struk_footer',   'value' => 'Terima kasih atas kunjungan Anda!'],
            ['key' => 'ppn_default',    'value' => '0'],
            ['key' => 'nota_header',    'value' => ''],
        ];
        foreach ($defaults as $d) {
            DB::table('settings')->insertOrIgnore(array_merge($d, ['created_at'=>now(),'updated_at'=>now()]));
        }
    }
    public function down(): void { Schema::dropIfExists('settings'); }
};
