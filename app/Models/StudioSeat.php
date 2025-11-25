<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudioSeat extends Model
{
    protected $guarded = ["id"];

    public function getSectionAttribute()
    {
        $sideSection = floor($this->studio->cols / 4);
        $centerSection = $this->studio->cols - $sideSection * 2;

        if ($this->col <= $sideSection) return 'A';
        if ($this->col > $sideSection && $this->col <= $sideSection + $centerSection) return 'B';
        return 'C';
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
