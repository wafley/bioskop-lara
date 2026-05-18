<?php

namespace App\Http\Controllers\Cashier;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $search = $request->search;

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
}
