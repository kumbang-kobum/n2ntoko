<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->decimal('qty_base', 15, 4);               // selalu dalam base unit
            $table->enum('movement_type', ['in', 'out', 'adjustment']);
            $table->string('reference_type');                  // purchase, sale, adjustment
            $table->unsignedBigInteger('reference_id');
            $table->decimal('stock_before', 15, 4);           // stok sebelum transaksi
            $table->decimal('stock_after', 15, 4);            // stok setelah transaksi
            $table->decimal('cost_per_unit', 15, 4)->default(0); // avg_cost saat transaksi
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};
