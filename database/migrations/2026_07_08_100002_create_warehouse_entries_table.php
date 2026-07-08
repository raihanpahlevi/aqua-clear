<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Modul Gudang — barang MASUK (pembelian/restock). Barang keluar TIDAK dicatat di sini,
// melainkan otomatis dari pemakaian di inventory_usage yang tertaut warehouse_item_id.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_item_id')->constrained('warehouse_items')->cascadeOnDelete();
            $table->date('tgl');
            $table->decimal('qty', 12, 2);
            $table->decimal('harga', 14, 2)->nullable(); // total harga pembelian entri ini (opsional)
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_entries');
    }
};
