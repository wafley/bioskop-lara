<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\Ticket;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    /**
     * Processing the creation of transactions and cinema tickets.
     *
     * @param array $data
     * @return Transaction
     * @throws \Exception
     */
    public function createBooking(array $data): Transaction
    {
        $schedule = Schedule::findOrFail($data['schedule_id']);

        $seats = Seat::whereIn('id', $data['seat_ids'])->get();

        $totalPrice = 0;
        $ticketDetails = [];

        foreach ($seats as $seat) {
            $vipSurcharge = (int) SettingHelper::get('vip_surcharge', 10000);
            $seatPrice = ($seat->type === 'vip') ? ($schedule->price + $vipSurcharge) : $schedule->price;
            $totalPrice += $seatPrice;

            $ticketDetails[] = [
                'seat_id' => $seat->id,
                'price'   => $seatPrice,
            ];
        }

        $amountPaid = $data['payment_method'] === 'cash' ? (int) $data['amount_paid'] : $totalPrice;
        if ($amountPaid < $totalPrice) {
            throw new \Exception('Nominal uang yang diterima kurang dari total bayar!');
        }

        $changeAmount = $amountPaid - $totalPrice;

        return DB::transaction(function () use ($schedule, $data, $seats, $totalPrice, $amountPaid, $changeAmount, $ticketDetails) {

            $isAnyBooked = Ticket::where('schedule_id', $schedule->id)
                ->whereIn('seat_id', $data['seat_ids'])
                ->whereIn('status', ['active', 'used'])
                ->exists();

            if ($isAnyBooked) {
                throw new \Exception('Salah satu kursi yang dipilih baru saja dipesan oleh kasir lain. Silakan pilih kursi kembali.');
            }

            $transaction = Transaction::create([
                'user_id'        => Auth::id(),
                'schedule_id'    => $schedule->id,
                'total_tickets'  => $seats->count(),
                'total_price'    => $totalPrice,
                'amount_paid'    => $amountPaid,
                'change_amount'  => $changeAmount,
                'payment_method' => $data['payment_method'],
                'status'         => 'success',
            ]);

            foreach ($ticketDetails as $detail) {
                $transaction->tickets()->create([
                    'schedule_id' => $schedule->id,
                    'seat_id'     => $detail['seat_id'],
                    'price'       => $detail['price'],
                    'status'      => 'active',
                ]);
            }

            return $transaction;
        });
    }
}
