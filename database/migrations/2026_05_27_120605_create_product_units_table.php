<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('unit_name');          // Kotak, Slop, Lusin, Karton, dll
            $table->decimal('conversion', 15, 4); // berapa base unit? (Kotak=1, Slop=10)
            $table->boolean('is_base')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'unit_name']);
        });

        // Setelah product_units ada, tambahkan FK base_unit_id ke products
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('base_unit_id')->references('id')->on('product_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
        });
        Schema::dropIfExists('product_units');
    }
};
