<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, CausesActivity;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value)
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn($value, $attributes) => $attributes['status'] ? 'success' : 'danger');
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn($value, $attributes) => $attributes['status'] ? 'Aktif' : 'Tidak Aktif');
    }

    /**
     * Relations
     */

    public function activities()
    {
        return $this->hasMany(Activity::class, 'causer_id')
            ->where('causer_type', self::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
