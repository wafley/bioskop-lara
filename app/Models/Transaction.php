<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (!$transaction->invoice_number) {
                $transaction->invoice_number = (string) generateInvoiceNumber();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'invoice_number';
    }

    /**
     * Relations
     */

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
