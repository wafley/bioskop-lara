<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $schedules = Schedule::with(['movie', 'studio'])
            ->where('show_date', $today)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('movie', function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('start_time', 'asc')
            ->get();

        return spaRender($request, 'booking.index', [
            'schedules' => $schedules,
        ]);
    }

    public function show(Request $request, Schedule $schedule)
    {

        $schedule->load(['movie', 'studio']);

        $title = sprintf(
            '%s (%s - %s)',
            $schedule->movie->title,
            substr($schedule->start_time, 0, 5),
            substr($schedule->end_time, 0, 5)
        );

        $seats = $schedule->studio->seats->groupBy('row');

        $bookedSeatIds = Ticket::where('schedule_id', $schedule->id)
            ->whereIn('status', ['active', 'used'])
            ->pluck('seat_id')
            ->toArray();

        return spaRender($request, 'booking.show', [
            'title'         => $title,
            'schedule'      => $schedule,
            'seats'         => $seats,
            'bookedSeatIds' => $bookedSeatIds,
        ]);
    }
}
