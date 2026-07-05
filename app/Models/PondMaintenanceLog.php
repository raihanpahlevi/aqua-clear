<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PondMaintenanceLog extends Model
{
    protected $fillable = [
        'stocking_id',
        'tgl',
        'siphon',
        'kondisi_lumpur',
        'jumlah_kincir',
        'jam_nyala_kincir',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tgl' => 'date',
            'siphon' => 'boolean',
        ];
    }

    public function stocking(): BelongsTo
    {
        return $this->belongsTo(Stocking::class);
    }
}
