<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($schedule) {
            if (!$schedule->uuid) {
                $schedule->uuid = (string) generateUniqueId();
            }
        });
    }

    /**
     * Relations
     */

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }
}
