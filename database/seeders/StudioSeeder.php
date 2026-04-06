<?php

namespace Database\Seeders;

use App\Models\Studio;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    public function run(): void
    {
        $studios = Studio::factory()->count(8)->create();

        foreach ($studios as $studio) {
            $rows = $studio->rows;
            $cols = $studio->cols;
            $capacity = $studio->capacity;

            $sideSection = floor($cols / 4);
            $centerSection = $cols - ($sideSection * 2);

            // Range Position
            $center_start = $sideSection + 1;
            $center_end   = $center_start + $centerSection - 1;

            $vipRow = (int) ceil($rows / 2);
            $vipCounter = 1;

            // Generate Seats
            for ($row = 1; $row <= $rows; $row++) {
                $rowLetter = chr(64 + $row);

                for ($col = 1; $col <= $cols; $col++) {
                    $type = 'regular';

                    if ($row === $vipRow && $col >= $center_start && $col <= $center_end) {
                        $type = 'vip';
                        $seatCode = 'VX' . $vipCounter;
                        $vipCounter++;
                    } else {
                        $seatCode = $rowLetter . str_pad($col, 2, '0', STR_PAD_LEFT);
                    }

                    Seat::create([
                        'seat_code' => $seatCode,
                        'type'      => $type,
                        'row'       => $row,
                        'col'       => $col,
                        'status'    => true,
                        'studio_id' => $studio->id,
                    ]);
                }
            }
        }
    }
}
