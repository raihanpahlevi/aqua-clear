<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ponds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('blocks')->cascadeOnDelete();
            $table->string('kode_kolam');
            $table->decimal('luas', 10, 2)->nullable(); // m2
            $table->decimal('kapasitas', 12, 2)->nullable();
            $table->enum('status', ['kosong', 'siap_tebar', 'aktif', 'panen', 'maintenance'])->default('kosong');
            $table->timestamps();

            $table->unique(['block_id', 'kode_kolam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ponds');
    }
};
