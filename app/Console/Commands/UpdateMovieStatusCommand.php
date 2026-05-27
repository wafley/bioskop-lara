<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateMovieStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update movie status based on its schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $updatedCount = 0;

        \App\Models\Movie::with('schedules')->chunk(100, function ($movies) use ($now, &$updatedCount) {
            foreach ($movies as $movie) {
                if ($movie->schedules->isEmpty()) {
                    continue;
                }

                $earliestStart = null;
                $latestEnd = null;

                foreach ($movie->schedules as $schedule) {
                    $start = \Carbon\Carbon::parse($schedule->show_date . ' ' . $schedule->start_time);
                    $end = \Carbon\Carbon::parse($schedule->show_date . ' ' . $schedule->end_time);

                    if (!$earliestStart || $start->lt($earliestStart)) {
                        $earliestStart = $start;
                    }
                    if (!$latestEnd || $end->gt($latestEnd)) {
                        $latestEnd = $end;
                    }
                }

                $newStatus = 'coming_soon';
                if ($now->between($earliestStart, $latestEnd)) {
                    $newStatus = 'now_showing';
                } elseif ($now->gt($latestEnd)) {
                    $newStatus = 'ended';
                }

                if ($movie->status !== $newStatus) {
                    $movie->update(['status' => $newStatus]);
                    $updatedCount++;
                }
            }
        });

        $this->info("Updated {$updatedCount} movies status.");

        if ($updatedCount > 0) {
            (new \App\Observers\ActivityObserver())->logCustom(
                message: "Cron: {$updatedCount} status film diperbarui",
                event: 'cron_movies_status',
                logName: 'system',
                properties: ['updated_count' => $updatedCount],
            );
        }
    }
}
