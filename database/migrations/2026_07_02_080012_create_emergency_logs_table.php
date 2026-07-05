<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->date('tgl');
            $table->string('jenis'); // udang sakit, air jelek, SR turun, dll — bebas, lihat CLAUDE.md
            $table->text('tindakan')->nullable();
            $table->enum('keputusan', ['lanjut', 'flush_out', 'panen_parsial'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_logs');
    }
};
