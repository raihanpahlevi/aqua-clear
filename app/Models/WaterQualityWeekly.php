<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterQualityWeekly extends Model
{
    protected $table = 'water_quality_weekly';

    protected $fillable = [
        'stocking_id',
        'tgl',
        'tan',
        'ammonia',
        'nitrit',
        'nitrat',
        'tom',
        'alkalinitas',
        'fe',
        'vibrio_hijau',
        'vibrio_hitam',
        'vibrio_luminer',
        'total_bakteri',
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
