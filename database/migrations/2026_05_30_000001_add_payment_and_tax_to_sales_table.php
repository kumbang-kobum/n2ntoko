<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'debit', 'qris'])->default('cash')->after('price_type');
            $table->decimal('subtotal_before_tax', 15, 2)->default(0)->after('total_amount');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('subtotal_before_tax');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
        });

        // Isi subtotal_before_tax dari total_amount untuk data lama (tax_rate = 0)
        DB::statement('UPDATE sales SET subtotal_before_tax = total_amount WHERE subtotal_before_tax = 0');
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'subtotal_before_tax', 'tax_rate', 'tax_amount']);
        });
    }
};
