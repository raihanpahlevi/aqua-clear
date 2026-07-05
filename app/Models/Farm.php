<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    protected $fillable = [
        'nama',
        'lokasi',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
