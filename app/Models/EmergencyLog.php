<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyLog extends Model
{
    protected $fillable = [
        'stocking_id',
        'tgl',
        'jenis',
        'tindakan',
        'keputusan',
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
