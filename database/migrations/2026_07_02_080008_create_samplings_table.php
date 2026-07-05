<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('samplings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->date('tgl');
            $table->unsignedInteger('doc'); // snapshot DOC saat sampling, dihitung service dari tgl_pakan_pertama

            // Input mentah — MBW dihitung di service (berat_sampel_total / jumlah_sampel)
            $table->decimal('berat_sampel_total', 10, 2); // gram
            $table->unsignedInteger('jumlah_sampel'); // ekor
            $table->decimal('mbw', 8, 3); // snapshot hasil hitung service, gram/ekor

            $table->unsignedInteger('populasi'); // estimasi populasi saat ini, dipakai hitung SR% & biomass
            $table->string('kondisi_organ')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('samplings');
    }
};
