<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Studio;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $movies = Movie::where('status', 'now_showing')->get();
        $studios = Studio::where('status', true)->get();

        if ($movies->isEmpty() || $studios->isEmpty()) return;

        $dates = [
            now()->format('Y-m-d'),
            now()->addDay()->format('Y-m-d'),
            now()->addDays(2)->format('Y-m-d')
        ];

        $schedulesData = [];

        foreach ($dates as $date) {
            $carbonDate = Carbon::parse($date);

            if ($carbonDate->isWeekend()) {
                $priceBase = 65000;
            } elseif ($carbonDate->isFriday()) {
                $priceBase = 50000;
            } else {
                $priceBase = 40000;
            }

            foreach ($studios as $studio) {
                $studioMovies = $movies->random(min(2, $movies->count()));

                $currentMinutes = 10 * 60;
                $endDayMinutes = 23 * 60;
                $movieIndex = 0;

                $price = (in_array($studio->name, ['VIP', 'Premiere', 'IMAX']))
                    ? $priceBase + 35000
                    : $priceBase;

                while ($currentMinutes < $endDayMinutes) {
                    $movie = $studioMovies[$movieIndex % $studioMovies->count()];
                    $duration = max(60, $this->parseDurationToMinutes($movie->getRawOriginal('duration')));

                    $startHour = floor($currentMinutes / 60);
                    $startMin = $currentMinutes % 60;

                    $endMinutes = $currentMinutes + $duration;

                    if ($endMinutes > ($endDayMinutes + 60)) break;

                    $endHour = floor($endMinutes / 60);
                    $endMin = $endMinutes % 60;

                    $schedulesData[] = [
                        'uuid'       => Str::uuid(),
                        'movie_id'   => $movie->id,
                        'studio_id'  => $studio->id,
                        'show_date'  => $date,
                        'start_time' => sprintf('%02d:%02d:00', $startHour, $startMin),
                        'end_time'   => sprintf('%02d:%02d:00', $endHour, $endMin),
                        'price'      => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $nextStart = $endMinutes + rand(20, 30);

                    $currentMinutes = ceil($nextStart / 15) * 15;

                    $movieIndex++;
                }
            }
        }

        foreach (array_chunk($schedulesData, 100) as $chunk) {
            Schedule::insert($chunk);
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
