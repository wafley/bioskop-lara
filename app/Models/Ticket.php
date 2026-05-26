<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn($value, array $attributes) => match ($attributes['status']) {
            'active' => (object) [
                'text' => 'Aktif',
                'class' => 'primary',
                'icon' => 'bi-ticket-perforated-fill'
            ],
            'used' => (object) [
                'text' => 'Sudah Digunakan',
                'class' => 'secondary',
                'icon' => 'bi-qr-code-scan'
            ],
            'refunded' => (object) [
                'text' => 'Dikembalikan (Refund)',
                'class' => 'danger',
                'icon' => 'bi-arrow-counterclockwise'
            ],
            default => (object) [
                'text' => ucfirst($this->status),
                'class' => 'light',
                'icon' => 'bi-info-circle'
            ],
        });
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
