<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\Schedule;
use Illuminate\Support\Str;
use App\Helpers\SettingHelper;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    protected $cleaningBuffer;

    public function __construct()
    {
        $this->cleaningBuffer = (int) SettingHelper::get('cleaning_buffer', 20);
    }

    public function run(): void
    {
        $studios = Studio::where('status', 1)->get();
        $movies = Movie::where('status', 'now_showing')->get();

        if ($movies->isEmpty()) {
            $this->command->warn("Tidak ada film 'now_showing'. Skipping Seeder.");
            return;
        }

        $moviePool = collect();
        $featuredMovies = $movies->count() > 1 ? $movies->random(2) : $movies;

        foreach ($movies as $movie) {
            $weight = $featuredMovies->contains('id', $movie->id) ? 3 : 1;
            for ($i = 0; $i < $weight; $i++) {
                $moviePool->push($movie);
            }
        }

        for ($day = 0; $day < 7; $day++) {
            $showDate = Carbon::today()->addDays($day);
            $dateString = $showDate->format('Y-m-d');

            $dailyQueue = $moviePool->shuffle();
            $movieIndex = 0;
            $totalInQueue = $dailyQueue->count();

            foreach ($studios as $studio) {
                $currentTime = $showDate->copy()->setTimeFromTimeString(
                    SettingHelper::get('opening_time', '10:00')
                );

                $closingTime = $showDate->copy()->setTimeFromTimeString(
                    SettingHelper::get('closing_time', '23:59')
                );

                $attempts = 0;
                $maxAttempts = $totalInQueue;

                while ($currentTime->lt($closingTime) && $attempts < $maxAttempts) {
                    $movie = $dailyQueue[$movieIndex % $totalInQueue];
                    $duration = max(60, $this->parseDurationToMinutes($movie->duration));

                    $startTime = $currentTime->copy();
                    $endTime = $startTime->copy()->addMinutes($duration);

                    if ($endTime->gt($closingTime)) {
                        break;
                    }

                    $isMovieBusy = Schedule::where('movie_id', $movie->id)
                        ->where('show_date', $dateString)
                        ->where(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<', $endTime->format('H:i:s'))
                                ->where('end_time', '>', $startTime->format('H:i:s'));
                        })
                        ->exists();

                    if ($isMovieBusy) {
                        $movieIndex++;
                        $attempts++;
                        continue;
                    }

                    $attempts = 0;

                    $weekdayPrice = (int) SettingHelper::get('weekday_prices', 40000);
                    $fridayPrice  = (int) SettingHelper::get('friday_prices', 50000);
                    $weekendPrice = (int) SettingHelper::get('weekend_prices', 65000);
                    $vipSurcharge = (int) SettingHelper::get('vip_surcharge', 35000);

                    $priceBase = match (true) {
                        $showDate->isFriday() => $fridayPrice,
                        $showDate->isWeekend() => $weekendPrice,
                        default => $weekdayPrice,
                    };

                    $finalPrice = (in_array($studio->name, ['VIP', 'Premiere', 'IMAX']))
                        ? $priceBase + $vipSurcharge
                        : $priceBase;

                    Schedule::create([
                        'uuid' => Str::uuid(),
                        'movie_id' => $movie->id,
                        'studio_id' => $studio->id,
                        'show_date' => $dateString,
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $endTime->format('H:i:s'),
                        'price' => $finalPrice,
                    ]);

                    $nextPossibleStartMinutes = ($endTime->hour * 60) + $endTime->minute + $this->cleaningBuffer;
                    $roundedMinutes = ceil($nextPossibleStartMinutes / 15) * 15;

                    $currentTime = $showDate->copy()->startOfDay()->addMinutes($roundedMinutes);

                    $movieIndex++;
                }
            }
        }
    }

    private function parseDurationToMinutes($duration): int
    {
        if (is_numeric($duration)) return (int) $duration;
        if (preg_match('/^(\d+):(\d+)$/', $duration, $matches)) {
            return ($matches[1] * 60) + $matches[2];
        }
        return 120;
    }
}
