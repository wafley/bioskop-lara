<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

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

    /**
     * Process order requests from the POS cashier page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id'    => 'required|exists:schedules,id',
            'seat_ids'       => 'required|array|min:1',
            'seat_ids.*'     => 'exists:studio_seats,id',
            'payment_method' => 'required|in:cash,transfer',
            'amount_paid'    => 'required_if:payment_method,cash|nullable|numeric|min:0',
        ]);

        try {
            $transaction = $this->bookingService->createBooking($validated);

            return response()->json([
                'status'        => 'success',
                'message'       => 'Transaksi berhasil diproses!',
                'redirect'      => route('transactions.print', $transaction->invoice_number),
                'redirect_type' => 'http'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
