<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateExpiredTicketsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update ticket status to used if schedule has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $count = 0;

        \App\Models\Ticket::where('status', 'active')
            ->with('schedule')
            ->chunkById(100, function ($tickets) use ($now, &$count) {
                foreach ($tickets as $ticket) {
                    if ($ticket->schedule) {
                        $end = \Carbon\Carbon::parse($ticket->schedule->show_date . ' ' . $ticket->schedule->end_time);
                        if ($now->gt($end)) {
                            $ticket->update(['status' => 'used']);
                            $count++;
                        }
                    }
                }
            });

        $this->info("Updated {$count} expired tickets to 'used'.");

        if ($count > 0) {
            (new \App\Observers\ActivityObserver())->logCustom(
                message: "Cron: {$count} tiket expired diperbarui ke 'used'",
                event: 'cron_tickets_expired',
                logName: 'system',
                properties: ['updated_count' => $count],
            );
        }
    }
}
