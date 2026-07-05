<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_quality_weekly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stocking_id')->constrained('stockings')->cascadeOnDelete();
            $table->date('tgl');

            // Mingguan (Bagian 5.3)
            $table->decimal('tan', 6, 3)->nullable();
            $table->decimal('ammonia', 6, 3)->nullable();
            $table->decimal('nitrit', 6, 3)->nullable();
            $table->decimal('nitrat', 6, 3)->nullable();

            // 10 hari sekali — uji lab, tanpa ambang pasti
            $table->decimal('tom', 8, 3)->nullable();
            $table->decimal('alkalinitas', 8, 3)->nullable();
            $table->decimal('fe', 8, 3)->nullable();

            // 7 hari sekali — vibrio hijau/hitam/luminer, rasio V/B dihitung di service
            $table->decimal('vibrio_hijau', 8, 2)->nullable();
            $table->decimal('vibrio_hitam', 8, 2)->nullable();
            $table->decimal('vibrio_luminer', 8, 2)->nullable();
            $table->decimal('total_bakteri', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_quality_weekly');
    }
};
