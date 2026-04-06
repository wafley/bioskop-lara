<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Studio;
use Carbon\Carbon;

class ScheduleService
{
    public function createSchedule(array $data)
    {
        $movie = Movie::findOrFail($data['movie_id']);
        $studio = Studio::findOrFail($data['studio_id']);

        $showDate = Carbon::createFromFormat('d-m-Y', $data['show_date']);
        $dateString = $showDate->format('Y-m-d');

        $priceBase = 40000;
        if ($showDate->isWeekend()) {
            $priceBase = 65000;
        } elseif ($showDate->isFriday()) {
            $priceBase = 50000;
        }

        $finalPrice = (in_array($studio->name, ['VIP', 'Premiere', 'IMAX']))
            ? $priceBase + 35000
            : $priceBase;

        $start = Carbon::parse($dateString . ' ' . $data['start_time']);
        $duration = max(60, $this->parseDurationToMinutes($movie->getRawOriginal('duration')));

        $startMinutes = ($start->hour * 60) + $start->minute;
        $endMinutes = $startMinutes + $duration;

        $endTimeFormatted = $start->copy()->startOfDay()->addMinutes($endMinutes)->format('H:i:s');

        $nextStartBuffer = $endMinutes + 20;
        $roundedNextStart = ceil($nextStartBuffer / 15) * 15;
        $limitTimeForOverlap = $start->copy()->startOfDay()->addMinutes($roundedNextStart)->format('H:i:s');

        if ($this->isOverlap($data['studio_id'], $dateString, $start->format('H:i:s'), $limitTimeForOverlap)) {
            throw new \Exception('Jadwal bentrok! Pastikan ada jeda minimal 20 menit untuk pembersihan studio sebelum jadwal berikutnya.');
        }

        return Schedule::create([
            'uuid'       => (string) \Illuminate\Support\Str::uuid(),
            'movie_id'   => $movie->id,
            'studio_id'  => $studio->id,
            'show_date'  => $dateString,
            'start_time' => $start->format('H:i:s'),
            'end_time'   => $endTimeFormatted,
            'price'      => $finalPrice,
        ]);
    }

    public function updateSchedule(Schedule $schedule, array $data)
    {
        $movie = Movie::findOrFail($data['movie_id']);
        $studio = Studio::findOrFail($data['studio_id']);

        $showDate = \Carbon\Carbon::createFromFormat('d-m-Y', $data['show_date']);
        $dateString = $showDate->format('Y-m-d');

        $priceBase = $showDate->isWeekend() ? 65000 : ($showDate->isFriday() ? 50000 : 40000);
        $finalPrice = (in_array($studio->name, ['VIP', 'Premiere', 'IMAX'])) ? $priceBase + 35000 : $priceBase;

        $start = \Carbon\Carbon::parse($dateString . ' ' . $data['start_time']);
        $duration = max(60, $this->parseDurationToMinutes($movie->getRawOriginal('duration')));

        $startMinutes = ($start->hour * 60) + $start->minute;
        $endMinutes = $startMinutes + $duration;

        $limitTimeForOverlap = $start->copy()->startOfDay()->addMinutes(ceil(($endMinutes + 20) / 15) * 15);

        if ($this->isOverlap($data['studio_id'], $dateString, $start->format('H:i:s'), $limitTimeForOverlap->format('H:i:s'), $schedule->uuid)) {
            throw new \Exception('Jadwal bentrok dengan jadwal lain di studio ini.');
        }

        return $schedule->update([
            'movie_id'   => $movie->id,
            'studio_id'  => $studio->id,
            'show_date'  => $dateString,
            'start_time' => $start->format('H:i:s'),
            'end_time'   => $start->copy()->startOfDay()->addMinutes($endMinutes)->format('H:i:s'),
            'price'      => $finalPrice,
        ]);
    }

    private function parseDurationToMinutes($duration): int
    {
        if (is_numeric($duration)) return (int) $duration;
        if (preg_match('/^(\d+):(\d+)$/', $duration, $matches)) {
            return ($matches[1] * 60) + $matches[2];
        }
        return 120;
    }

    public function isOverlap($studioId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = Schedule::where('studio_id', $studioId)
            ->where('show_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });

        if ($excludeId) {
            $query->where('uuid', '!=', $excludeId);
        }

        return $query->exists();
    }
}
