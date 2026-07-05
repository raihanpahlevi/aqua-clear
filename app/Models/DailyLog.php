<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    protected $fillable = [
        'stocking_id',
        'tgl',
        'pakan_07_kg',
        'pakan_11_kg',
        'pakan_15_kg',
        'pakan_19_kg',
        'kode_pakan',
        'ancho_07',
        'ancho_11',
        'ancho_15',
        'ancho_19',
        'do_pagi',
        'do_sore',
        'ph_pagi',
        'ph_sore',
        'suhu_pagi',
        'suhu_sore',
        'salinitas',
        'mortalitas',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tgl' => 'date',
        ];
    }

    public function stocking(): BelongsTo
    {
        return $this->belongsTo(Stocking::class);
    }
}
