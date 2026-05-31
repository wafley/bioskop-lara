<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Schedule;
use App\Services\BookingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashiers = User::whereHas('role', function ($q) {
            $q->where('name', 'cashier');
        })->get();

        if ($cashiers->isEmpty()) {
            return;
        }

        // Get schedules. Optionally, limit to schedules from a few days ago up to next few days
        // to make the seeder run faster if there are many schedules.
        $schedules = Schedule::with('studio.seats')->get();
        
        // Mock the ActivityObserver logic by letting it run, it uses Auth::id() via user(), but we are logging in.
        $bookingService = app(BookingService::class);

        foreach ($schedules as $schedule) {
            // Decide 0 to 5 transactions for this schedule
            $numTransactions = rand(1, 5);
            $availableSeats = $schedule->studio->seats->pluck('id')->toArray();

            for ($i = 0; $i < $numTransactions; $i++) {
                if (count($availableSeats) < 5) {
                    break;
                }

                $cashier = $cashiers->random();
                Auth::login($cashier);

                $numTickets = rand(1, 4);
                $seatKeys = array_rand($availableSeats, $numTickets);
                $seatIds = is_array($seatKeys) 
                    ? array_map(fn($k) => $availableSeats[$k], $seatKeys) 
                    : [$availableSeats[$seatKeys]];

                // Remove used seats
                $availableSeats = array_values(array_diff($availableSeats, $seatIds));

                $paymentMethod = rand(0, 1) ? 'cash' : 'transfer';
                
                // For cash, provide a generous amount to cover the tickets
                $amountPaid = $paymentMethod === 'cash' ? 1000000 : null;

                try {
                    $bookingService->createBooking([
                        'schedule_id'    => $schedule->id,
                        'seat_ids'       => $seatIds,
                        'payment_method' => $paymentMethod,
                        'amount_paid'    => $amountPaid,
                    ]);
                } catch (\Exception $e) {
                    // Silently fail for individual bookings in seeder if e.g. price mismatch
                }
            }
        }
    }
}
