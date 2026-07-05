<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sampling extends Model
{
    protected $fillable = [
        'stocking_id',
        'tgl',
        'doc',
        'berat_sampel_total',
        'jumlah_sampel',
        'mbw',
        'populasi',
        'kondisi_organ',
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
