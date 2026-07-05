<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->date('tgl');
            $table->enum('kategori', ['probiotik', 'mineral', 'desinfektan', 'obat']);
            $table->string('item'); // nama bahan — daftar & harga acuan masih terbuka, lihat CLAUDE.md
            $table->decimal('qty', 10, 2);
            $table->string('satuan')->nullable();
            $table->decimal('harga', 12, 2)->nullable(); // total biaya entri ini — dikosongkan dulu bila harga belum tersedia
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_usage');
    }
};
