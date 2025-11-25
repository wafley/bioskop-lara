<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Movie extends Model
{
    protected $guarded = ['id'];

    public const GENRES = [
        'action' => 'Action',
        'adventure' => 'Adventure',
        'animation' => 'Animation',
        'comedy' => 'Comedy',
        'documentary' => 'Documentary',
        'drama' => 'Drama',
        'fantasy' => 'Fantasy',
        'horror' => 'Horror',
        'musical' => 'Musical',
        'mystery' => 'Mystery',
        'romance' => 'Romance',
        'sci_fi' => 'Sci-Fi',
        'thriller' => 'Thriller',
        'western' => 'Western',
    ];

    protected static function booted()
    {
        static::saving(function ($movie) {
            if ($movie->isDirty('title')) {
                $movie->slug = generateSlug($movie, $movie->title);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value)
        );
    }

    protected function genre(): Attribute
    {
        return Attribute::make(
            get: fn($value) => array_map(fn($item) => Str::title($item), explode(',', $value)),
            set: fn($value) => is_array($value) ? implode(',', $value) : $value
        );
    }

    protected function cast(): Attribute
    {
        return Attribute::make(
            get: fn($value) => array_map(fn($item) => Str::title($item), explode(',', $value)),
            set: fn($value) => is_array($value) ? implode(',', $value) : $value
        );
    }

    protected function director(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::title($value)
        );
    }

    protected function releaseDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('d-m-Y') : null,
            set: fn($value) => $value ? Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d') : null
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn($value) => formatDuration($value),
            set: function ($value) {
                if (!$value) {
                    return null;
                }

                if (preg_match('/^(\d{1,2}):(\d{1,2})$/', $value, $match)) {
                    $hours   = (int) $match[1];
                    $minutes = (int) $match[2];
                    return ($hours * 60) + $minutes;
                }

                if (is_numeric($value)) {
                    return (int) $value;
                }

                return null;
            }
        );
    }

    protected function poster(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value && Storage::disk('public')->exists('movies/' . $value)) {
                    return asset('storage/movies/' . $value);
                }
                return asset('assets/images/placeholders/poster-placeholder.png');
            },
            set: function ($value, $attributes) {
                if ($value && is_a($value, \Illuminate\Http\UploadedFile::class)) {
                    if (!empty($attributes['poster']) && Storage::disk('public')->exists('movies/' . $attributes['poster'])) {
                        Storage::disk('public')->delete('movies/' . $attributes['poster']);
                    }

                    $title = $attributes['title'] ?? 'file';
                    $extension = $value->getClientOriginalExtension();
                    $filename = time() . '_' . Str::slug($title) . '.' . $extension;

                    $value->storeAs('movies', $filename, 'public');
                    return $filename;
                }

                return $value;
            }
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            return match ($attributes['status']) {
                'coming_soon' => 'warning',
                'now_showing' => 'success',
                'ended'       => 'secondary',
                default       => 'dark',
            };
        });
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(function ($value, $attributes) {
            return match ($attributes['status']) {
                'coming_soon' => 'Segera Tayang',
                'now_showing' => 'Tersedia',
                'ended'       => 'Selesai',
                default       => 'Tidak Diketahui',
            };
        });
    }
}
