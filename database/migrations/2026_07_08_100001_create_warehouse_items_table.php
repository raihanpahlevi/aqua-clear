<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Modul Gudang (fase 2, permintaan client 2026-07-08) — master barang gudang.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('nama', 150);
            // Kategori sama dengan inventory_usage.kategori supaya pemakaian bisa disambungkan
            $table->enum('kategori', ['pakan', 'probiotik', 'mineral', 'desinfektan', 'obat']);
            $table->string('satuan', 30); // kg, liter, sak, botol, dst.
            $table->timestamps();

            $table->unique(['farm_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};
