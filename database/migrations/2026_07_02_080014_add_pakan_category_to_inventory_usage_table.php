<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory_usage MODIFY kategori ENUM('probiotik', 'mineral', 'desinfektan', 'obat', 'pakan') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory_usage MODIFY kategori ENUM('probiotik', 'mineral', 'desinfektan', 'obat') NOT NULL");
    }
};
