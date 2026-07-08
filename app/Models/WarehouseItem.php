<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseItem extends Model
{
    protected $fillable = [
        'farm_id',
        'nama',
        'kategori',
        'satuan',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(WarehouseEntry::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(InventoryUsage::class);
    }
}
