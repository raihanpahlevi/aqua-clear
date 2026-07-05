<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stockings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_id')->constrained('ponds')->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('cycles')->cascadeOnDelete();
            $table->date('tgl_tebar');
            $table->date('tgl_pakan_pertama')->nullable(); // anchor DOC — lihat CLAUDE.md
            $table->string('asal_benur')->nullable();
            $table->unsignedInteger('jumlah_tebar');
            $table->decimal('harga_benur', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stockings');
    }
};
