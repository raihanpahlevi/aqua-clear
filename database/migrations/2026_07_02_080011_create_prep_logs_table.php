<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prep_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pond_id')->constrained('ponds')->cascadeOnDelete();
            $table->foreignId('cycle_id')->nullable()->constrained('cycles')->nullOnDelete();
            $table->enum('jenis', ['tambak', 'air']);
            $table->date('tgl');
            $table->jsonb('checklist')->nullable(); // checklist bebas, tanpa validasi ketat — lihat CLAUDE.md
            $table->decimal('biaya', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prep_logs');
    }
};
