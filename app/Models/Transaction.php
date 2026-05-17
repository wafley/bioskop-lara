<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::get(fn($value, array $attributes) => match ($attributes['payment_method']) {
            'cash' => 'Tunai',
            'transfer' => 'Transfer Bank',
            default => ucfirst($attributes['payment_method']),
        });
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn($value, array $attributes) => match ($attributes['status']) {
            'success' => (object) [
                'text' => 'Sukses',
                'class' => 'success',
                'icon' => 'bi-check-circle-fill'
            ],
            'pending' => (object) [
                'text' => 'Menunggu Pembayaran',
                'class' => 'warning',
                'icon' => 'bi-hourglass-split'
            ],
            'cancelled' => (object) [
                'text' => 'Dibatalkan',
                'class' => 'danger',
                'icon' => 'bi-x-circle-fill'
            ],
            default => (object) [
                'text' => ucfirst($attributes['status']),
                'class' => 'secondary',
                'icon' => 'bi-info-circle'
            ],
        });
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
