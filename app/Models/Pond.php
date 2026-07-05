<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pond extends Model
{
    protected $fillable = [
        'block_id',
        'kode_kolam',
        'luas',
        'kapasitas',
        'status',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function stockings(): HasMany
    {
        return $this->hasMany(Stocking::class);
    }

    public function prepLogs(): HasMany
    {
        return $this->hasMany(PrepLog::class);
    }
}
