<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\Schedule;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    protected $cleaningBuffer = 20;

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
                $currentTime = $showDate->copy()->setTime(10, 0, 0);
                $closingTime = $showDate->copy()->setTime(23, 59, 0);

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

                    $priceBase = match (true) {
                        $showDate->isFriday() => 50000,
                        $showDate->isWeekend() => 65000,
                        default => 40000,
                    };

                    $finalPrice = (in_array($studio->name, ['VIP', 'Premiere', 'IMAX']))
                        ? $priceBase + 35000
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
