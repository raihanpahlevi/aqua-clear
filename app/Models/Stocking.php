<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stocking extends Model
{
    protected $fillable = [
        'pond_id',
        'cycle_id',
        'tgl_tebar',
        'tgl_pakan_pertama',
        'asal_benur',
        'jumlah_tebar',
        'harga_benur',
    ];

    protected function casts(): array
    {
        return [
            'tgl_tebar' => 'date',
            'tgl_pakan_pertama' => 'date',
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

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function waterQualityWeeklies(): HasMany
    {
        return $this->hasMany(WaterQualityWeekly::class);
    }

    public function samplings(): HasMany
    {
        return $this->hasMany(Sampling::class);
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(Harvest::class);
    }

    public function inventoryUsages(): HasMany
    {
        return $this->hasMany(InventoryUsage::class);
    }

    public function emergencyLogs(): HasMany
    {
        return $this->hasMany(EmergencyLog::class);
    }

    public function pondMaintenanceLogs(): HasMany
    {
        return $this->hasMany(PondMaintenanceLog::class);
    }
}
