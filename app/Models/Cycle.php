<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cycle extends Model
{
    protected $fillable = [
        'nama',
    ];

    public function stockings(): HasMany
    {
        return $this->hasMany(Stocking::class);
    }

    public function prepLogs(): HasMany
    {
        return $this->hasMany(PrepLog::class);
    }
}
