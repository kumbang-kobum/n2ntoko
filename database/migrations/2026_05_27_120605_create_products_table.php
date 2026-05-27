<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('base_unit_id')->nullable(); // FK ke product_units, set setelah insert units
            $table->decimal('stock_qty', 15, 4)->default(0);        // stok dalam base unit
            $table->decimal('avg_cost', 15, 4)->default(0);         // harga pokok rata-rata per base unit
            $table->decimal('min_stock', 15, 4)->default(0);        // batas stok minimum (alert)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
