<?php

namespace App\Services;

use App\Helpers\SettingHelper;
use Illuminate\Support\Str;
use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Studio;
use Carbon\Carbon;

class ScheduleService
{
    protected $openingTime;
    protected $closingTime;
    protected $cleaningBuffer;

    public function __construct()
    {
        $this->openingTime = SettingHelper::get('opening_time', '10:00');
        $this->closingTime = SettingHelper::get('closing_time', '23:59');
        $this->cleaningBuffer = (int) SettingHelper::get('cleaning_buffer', 20);
    }

    /**
     * Create a new schedule entry.
     * 
     * @param array $data
     * @return Schedule
     * @throws \Exception
     */
    public function createSchedule(array $data)
    {
        $movie = Movie::findOrFail($data['movie_id']);
        $studio = Studio::findOrFail($data['studio_id']);

        $showDate = Carbon::createFromFormat('d-m-Y', $data['show_date']);
        $dateString = $showDate->format('Y-m-d');

        $start = Carbon::parse($dateString . ' ' . $data['start_time']);
        $duration = max(60, $this->parseDurationToMinutes($movie->getRawOriginal('duration')));

        // Validate operational hours
        $this->validateOperationalTime($start, $duration);

        // Calculate end time and next available start time with cleaning buffer
        $endTimeFormatted = $start->copy()->addMinutes($duration)->format('H:i:s');
        $roundedNextStart = ceil(($start->diffInMinutes($start->copy()->startOfDay()->addMinutes($start->hour * 60 + $start->minute + $duration)) + $this->cleaningBuffer) / 15) * 15;

        // Calculate the limit time for overlap checking, which includes the cleaning buffer
        $limitTimeForOverlap = $start->copy()->startOfDay()->addMinutes(($start->hour * 60) + $start->minute + $duration + $this->cleaningBuffer)->format('H:i:s');

        // Check for schedule overlap in the same studio, considering the cleaning buffer
        if ($this->isOverlap($data['studio_id'], $dateString, $start->format('H:i:s'), $limitTimeForOverlap)) {
            throw new \Exception("Jadwal bentrok! Pastikan ada jeda minimal {$this->cleaningBuffer} menit untuk pembersihan studio.");
        }

        // Check if the same movie is already scheduled at the same time in another studio
        if ($this->isMovieBusy($movie->id, $dateString, $start->format('H:i:s'), $endTimeFormatted)) {
            throw new \Exception('Film ini sudah dijadwalkan tayang di studio lain pada jam yang sama.');
        }

        // Calculate price based on show date and studio type
        $finalPrice = $this->calculatePrice($showDate, $studio->name);

        return Schedule::create([
            'uuid'       => (string) Str::uuid(),
            'movie_id'   => $movie->id,
            'studio_id'  => $studio->id,
            'show_date'  => $dateString,
            'start_time' => $start->format('H:i:s'),
            'end_time'   => $endTimeFormatted,
            'price'      => $finalPrice,
        ]);
    }

    /**
     * Update an existing schedule entry.
     * 
     * @param Schedule $schedule
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function updateSchedule(Schedule $schedule, array $data)
    {
        $movie = Movie::findOrFail($data['movie_id']);
        $studio = Studio::findOrFail($data['studio_id']);

        $showDate = Carbon::createFromFormat('d-m-Y', $data['show_date']);
        $dateString = $showDate->format('Y-m-d');

        $start = Carbon::parse($dateString . ' ' . $data['start_time']);
        $duration = max(60, $this->parseDurationToMinutes($movie->getRawOriginal('duration')));

        // Validate operational hours
        $this->validateOperationalTime($start, $duration);

        $endTimeFormatted = $start->copy()->addMinutes($duration)->format('H:i:s');
        $limitTimeForOverlap = $start->copy()->startOfDay()->addMinutes(($start->hour * 60) + $start->minute + $duration + $this->cleaningBuffer)->format('H:i:s');

        // Check for schedule overlap in the same studio, considering the cleaning buffer, and excluding the current schedule
        if ($this->isOverlap($data['studio_id'], $dateString, $start->format('H:i:s'), $limitTimeForOverlap, $schedule->uuid)) {
            throw new \Exception('Jadwal bentrok dengan jadwal lain di studio ini.');
        }

        if ($this->isMovieBusy($movie->id, $dateString, $start->format('H:i:s'), $endTimeFormatted, $schedule->uuid)) {
            throw new \Exception('Film ini sudah dijadwalkan tayang di studio lain pada jam yang sama.');
        }

        return $schedule->update([
            'movie_id'   => $movie->id,
            'studio_id'  => $studio->id,
            'show_date'  => $dateString,
            'start_time' => $start->format('H:i:s'),
            'end_time'   => $endTimeFormatted,
            'price'      => $this->calculatePrice($showDate, $studio->name),
        ]);
    }

    /**
     * Validate operational time for a given schedule.
     *
     * @param Carbon $start
     * @param int $duration
     * @return void
     * @throws \Exception
     */
    private function validateOperationalTime(Carbon $start, int $duration)
    {
        $opening = Carbon::parse($start->format('Y-m-d') . ' ' . $this->openingTime);
        $closing = Carbon::parse($start->format('Y-m-d') . ' ' . $this->closingTime);
        $end = $start->copy()->addMinutes($duration);

        if ($start->lt($opening)) {
            throw new \Exception("Jam tayang tidak valid. Bioskop baru buka jam {$this->openingTime}.");
        }

        if ($end->gt($closing)) {
            throw new \Exception("Jam tayang melebihi jam operasional. Bioskop tutup jam {$this->closingTime}.");
        }
    }

    /**
     * Calculate ticket price based on show date and studio type.
     *
     * @param Carbon $showDate
     * @param string $studioName
     * @return int
     * @throws \Exception
     */
    private function calculatePrice(Carbon $showDate, string $studioName): int
    {
        $weekdayPrice = (int) SettingHelper::get('weekday_prices', 40000);
        $fridayPrice  = (int) SettingHelper::get('friday_prices', 50000);
        $weekendPrice = (int) SettingHelper::get('weekend_prices', 65000);
        $vipSurcharge = (int) SettingHelper::get('vip_surcharge', 35000);

        $priceBase = match (true) {
            $showDate->isFriday() => $fridayPrice,
            $showDate->isWeekend() => $weekendPrice,
            default => $weekdayPrice,
        };

        return (in_array($studioName, ['VIP', 'Premiere', 'IMAX']))
            ? $priceBase + $vipSurcharge
            : $priceBase;
    }

    /**
     * Check if a movie is already scheduled at the same time in another studio.
     *
     * @param int $movieId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param string|null $excludeId
     * @return bool
     */
    public function isMovieBusy($movieId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = Schedule::where('movie_id', $movieId)
            ->where('show_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });

        if ($excludeId) $query->where('uuid', '!=', $excludeId);

        return $query->exists();
    }

    /**
     * Check if a schedule overlaps with existing schedules in the same studio.
     *
     * @param int $studioId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param string|null $excludeId
     * @return bool
     */
    public function isOverlap($studioId, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = Schedule::where('studio_id', $studioId)
            ->where('show_date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            });

        if ($excludeId) $query->where('uuid', '!=', $excludeId);

        return $query->exists();
    }

    /**
     * Parse duration to minutes. Accepts either numeric minutes or "HH:MM" format.
     * 
     * @param mixed $duration
     * @return int
     * @throws \Exception
     */
    private function parseDurationToMinutes($duration): int
    {
        if (is_numeric($duration)) return (int) $duration;
        if (preg_match('/^(\d+):(\d+)$/', $duration, $matches)) {
            return ($matches[1] * 60) + $matches[2];
        }
        return 120;
    }
}
