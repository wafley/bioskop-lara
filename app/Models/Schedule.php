<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function getStatusLabelAttribute()
    {
        return match ($this->movie->status) {
            'now_showing' => (object) [
                'text' => 'Sedang Tayang',
                'class' => 'warning',
                'icon' => 'bi-play-fill'
            ],
            'ended' => (object) [
                'text' => 'Selesai',
                'class' => 'success',
                'icon' => 'bi-check-circle'
            ],
            default => (object) [
                'text' => 'Segera Tayang',
                'class' => 'secondary',
                'icon' => 'bi-calendar-event'
            ],
        };
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Relations
     */

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
