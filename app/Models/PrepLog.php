<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrepLog extends Model
{
    protected $fillable = [
        'pond_id',
        'cycle_id',
        'jenis',
        'tgl',
        'checklist',
        'biaya',
    ];

    protected function casts(): array
    {
        return [
            'tgl' => 'date',
            'checklist' => 'array',
        ];
    }

    public function pond(): BelongsTo
    {
        return $this->belongsTo(Pond::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }
}
