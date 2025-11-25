<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Studio extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saving(function ($studio) {
            if ($studio->isDirty('name')) {
                $studio->slug = generateSlug($studio, $studio->name);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn($value, $attributes) => $attributes['status'] ? 'success' : 'danger');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status ? 'Open' : 'Closed'
        );
    }

    public function seats()
    {
        return $this->hasMany(StudioSeat::class);
    }
}
