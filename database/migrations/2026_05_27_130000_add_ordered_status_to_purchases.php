<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE purchases MODIFY COLUMN status ENUM('draft','ordered','confirmed','paid') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE purchases MODIFY COLUMN status ENUM('draft','confirmed','paid') DEFAULT 'draft'");
    }
};
