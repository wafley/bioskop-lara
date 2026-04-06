<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getStatusLabelAttribute()
    {
        $now = now();
        $start = Carbon::parse($this->show_date . ' ' . $this->start_time);
        $end = Carbon::parse($this->show_date . ' ' . $this->end_time);

        if ($now->between($start, $end)) {
            return (object) [
                'text' => 'Sedang Tayang',
                'class' => 'warning',
                'icon' => 'bi-play-fill'
            ];
        }

        if ($now->gt($end)) {
            return (object) [
                'text' => 'Selesai',
                'class' => 'success',
                'icon' => 'bi-check-circle'
            ];
        }

        return (object) [
            'text' => 'Segera Tayang',
            'class' => 'secondary',
            'icon' => 'bi-calendar-event'
        ];
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
