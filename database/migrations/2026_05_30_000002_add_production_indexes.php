<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Helper: hanya tambah index jika belum ada
        $addIndex = function (string $table, array $cols, ?string $name = null) {
            $indexName = $name ?? ($table . '_' . implode('_', $cols) . '_index');
            $exists = collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]))->isNotEmpty();
            if (!$exists) {
                Schema::table($table, function (Blueprint $t) use ($cols, $indexName) {
                    $t->index($cols, $indexName);
                });
            }
        };

        // sale_items
        $addIndex('sale_items',     ['product_id', 'created_at']);

        // purchase_items
        $addIndex('purchase_items', ['product_id', 'created_at']);
        $addIndex('purchase_items', ['purchase_id', 'product_id']);

        // stock_ledgers
        $addIndex('stock_ledgers',  ['reference_type', 'reference_id']);
        $addIndex('stock_ledgers',  ['movement_type', 'created_at']);

        // sales
        $addIndex('sales', ['sale_date', 'status']);
        $addIndex('sales', ['user_id', 'sale_date']);
        $addIndex('sales', ['payment_method', 'sale_date']);
        $addIndex('sales', ['status', 'created_at']);

        // purchases
        $addIndex('purchases', ['purchase_date', 'status']);
        $addIndex('purchases', ['supplier_id', 'purchase_date']);
        $addIndex('purchases', ['status', 'created_at']);

        // products
        $addIndex('products', ['category_id', 'is_active']);
        $addIndex('products', ['is_active', 'stock_qty']);

        // expenses
        $addIndex('expenses', ['expense_date', 'category']);
        $addIndex('expenses', ['expense_date']);
    }

    public function down(): void
    {
        $drop = function (string $table, array $cols) {
            $name = $table . '_' . implode('_', $cols) . '_index';
            Schema::table($table, fn(Blueprint $t) => $t->dropIndexIfExists($name));
        };

        $drop('sale_items',     ['product_id', 'created_at']);
        $drop('purchase_items', ['product_id', 'created_at']);
        $drop('purchase_items', ['purchase_id', 'product_id']);
        $drop('stock_ledgers',  ['reference_type', 'reference_id']);
        $drop('stock_ledgers',  ['movement_type', 'created_at']);
        $drop('sales',          ['sale_date', 'status']);
        $drop('sales',          ['user_id', 'sale_date']);
        $drop('sales',          ['payment_method', 'sale_date']);
        $drop('sales',          ['status', 'created_at']);
        $drop('purchases',      ['purchase_date', 'status']);
        $drop('purchases',      ['supplier_id', 'purchase_date']);
        $drop('purchases',      ['status', 'created_at']);
        $drop('products',       ['category_id', 'is_active']);
        $drop('products',       ['is_active', 'stock_qty']);
        $drop('expenses',       ['expense_date', 'category']);
        $drop('expenses',       ['expense_date']);
    }
};
