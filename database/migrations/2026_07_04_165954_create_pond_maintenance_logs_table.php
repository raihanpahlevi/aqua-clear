<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pond_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->date('tgl');
            $table->boolean('siphon')->nullable(); // dilakukan siphon atau tidak hari itu
            $table->string('kondisi_lumpur')->nullable(); // kualitatif: baik/sedang/buruk, bebas — lihat CLAUDE.md
            $table->unsignedInteger('jumlah_kincir')->nullable();
            $table->decimal('jam_nyala_kincir', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['stocking_id', 'tgl']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pond_maintenance_logs');
    }
};
