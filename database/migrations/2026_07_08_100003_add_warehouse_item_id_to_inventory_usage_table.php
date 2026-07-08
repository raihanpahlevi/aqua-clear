<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tautan opsional pemakaian → barang gudang: satu kali input pemakaian di kolam
// otomatis mengurangi saldo gudang (keputusan user 2026-07-08: "nyambung", bukan dobel input).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_usage', function (Blueprint $table) {
            $table->foreignId('warehouse_item_id')->nullable()->after('stocking_id')
                ->constrained('warehouse_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_usage', function (Blueprint $table) {
            $table->dropConstrainedForeignId('warehouse_item_id');
        });
    }
};
