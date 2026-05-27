<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('product_units')->restrictOnDelete();
            $table->decimal('qty', 15, 4);
            $table->decimal('qty_base', 15, 4);         // qty dikonversi ke base unit
            $table->decimal('sell_price', 15, 2);       // harga jual per satuan
            $table->decimal('hpp_snapshot', 15, 4);     // avg_cost saat transaksi (dibekukan)
            $table->decimal('subtotal', 15, 2);
            $table->decimal('profit', 15, 2);           // subtotal - (qty_base * hpp_snapshot)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
