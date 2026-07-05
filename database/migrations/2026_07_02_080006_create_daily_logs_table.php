<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->date('tgl');

            // Pakan 4x/hari — 07.00, 11.00, 15.00, 19.00
            $table->decimal('pakan_07_kg', 8, 2)->nullable();
            $table->decimal('pakan_11_kg', 8, 2)->nullable();
            $table->decimal('pakan_15_kg', 8, 2)->nullable();
            $table->decimal('pakan_19_kg', 8, 2)->nullable();
            $table->string('kode_pakan')->nullable(); // sesuai fase DOC, #0 -> 3M

            // Ancho dicek ±2 jam setelah tiap sesi pakan (asumsi sementara — lihat CLAUDE.md)
            $table->enum('ancho_07', ['habis', 'sisa_sedikit', 'sisa_banyak'])->nullable();
            $table->enum('ancho_11', ['habis', 'sisa_sedikit', 'sisa_banyak'])->nullable();
            $table->enum('ancho_15', ['habis', 'sisa_sedikit', 'sisa_banyak'])->nullable();
            $table->enum('ancho_19', ['habis', 'sisa_sedikit', 'sisa_banyak'])->nullable();

            // Kualitas air harian (Bagian 5.3)
            $table->decimal('do_pagi', 5, 2)->nullable();
            $table->decimal('do_sore', 5, 2)->nullable();
            $table->decimal('ph_pagi', 4, 2)->nullable();
            $table->decimal('ph_sore', 4, 2)->nullable();
            $table->decimal('suhu_pagi', 4, 2)->nullable();
            $table->decimal('suhu_sore', 4, 2)->nullable();
            $table->decimal('salinitas', 5, 2)->nullable(); // 1x/hari, bukan pagi-sore

            // Mortalitas mentah hasil observasi — dikali 2 di service, lihat CLAUDE.md
            $table->unsignedInteger('mortalitas')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['stocking_id', 'tgl']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
