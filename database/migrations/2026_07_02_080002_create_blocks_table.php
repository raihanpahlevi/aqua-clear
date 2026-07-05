<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->string('nama'); // A, B, C, D, R, RW
            $table->timestamps();

            $table->unique(['farm_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
