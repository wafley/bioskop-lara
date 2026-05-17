<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (!$ticket->ticket_code) {
                $ticket->ticket_code = (string) generateUniqueId(16);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'ticket_code';
    }

    /**
     * Relations
     */

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class, 'seat_id');
    }
}
