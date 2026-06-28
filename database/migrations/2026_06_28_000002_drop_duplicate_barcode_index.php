<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            // unique() di migration awal sudah membuat index 'product_barcodes_barcode_unique'.
            // index('barcode') di bawahnya membuat index reguler kedua yang redundan.
            $table->dropIndex('product_barcodes_barcode_index');
        });
    }

    public function down(): void
    {
        Schema::table('product_barcodes', function (Blueprint $table) {
            $table->index('barcode');
        });
    }
};
