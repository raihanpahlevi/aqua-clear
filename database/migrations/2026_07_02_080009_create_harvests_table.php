<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->enum('tahap', ['partial1', 'partial2', 'total']);
            $table->date('tgl');
            $table->decimal('berat_kg', 10, 2);
            $table->decimal('size', 8, 2)->nullable(); // ekor/kg diukur langsung saat panen
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('pendapatan', 14, 2); // snapshot hasil hitung service: berat_kg * harga_per_kg
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
