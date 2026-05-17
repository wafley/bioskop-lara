<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seat extends Model
{
    protected $table = "studio_seats";
    protected $guarded = ["id"];

    public function getSectionAttribute()
    {
        $sideSection = floor($this->studio->cols / 4);
        $centerSection = $this->studio->cols - $sideSection * 2;

        if ($this->col <= $sideSection) return 'A';
        if ($this->col > $sideSection && $this->col <= $sideSection + $centerSection) return 'B';
        return 'C';
    }

    /**
     * Relations
     */

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'seat_id');
    }
}
