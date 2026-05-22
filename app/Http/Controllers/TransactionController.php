<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\BookingService;

class TransactionController extends Controller
{
    protected BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
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

    /**
     * Hub Page / Print Options
     */
    public function print(Transaction $transaction)
    {
        return view('transactions.print', compact('transaction'));
    }

    /**
     * Special Print Receipt / Payment Receipt
     */
    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['schedule.movie', 'schedule.studio', 'cashier']);
        return view('transactions.print-receipt', compact('transaction'));
    }

    /**
     * Special Printed Cinema Tickets (Per Seat)
     */
    public function printTicket(Transaction $transaction)
    {
        $transaction->load(['schedule.movie', 'schedule.studio', 'tickets.seat']);
        return view('transactions.print-ticket', compact('transaction'));
    }
}
