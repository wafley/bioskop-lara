<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\Studio;
use Illuminate\Support\Facades\DB;

class StudioService
{
    /**
     * Save new studio data and its chairs.
     */
    public function createStudio(array $data, bool $generateVip): bool
    {
        DB::beginTransaction();
        try {
            $data['capacity'] = $data['rows'] * $data['cols'];
            $studio = Studio::create($data);

            $this->generateSeatsForStudio($studio, $generateVip);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Helper method to generate chair array and insert into database.
     */
    private function generateSeatsForStudio(Studio $studio, bool $generateVip): void
    {
        $rows = $studio->rows;
        $cols = $studio->cols;
        $sideSection = floor($cols / 4);
        $centerSection = $cols - ($sideSection * 2);

        $center_start = $sideSection + 1;
        $center_end   = $center_start + $centerSection - 1;

        $vipRow = (int) ceil($rows / 2);
        $vipCounter = 1;

        $seats = [];

        for ($row = 1; $row <= $rows; $row++) {
            $rowLetter = chr(64 + $row);

            for ($col = 1; $col <= $cols; $col++) {
                $type = 'regular';

                if ($generateVip && $row === $vipRow && $col >= $center_start && $col <= $center_end) {
                    $type = 'vip';
                    $seatCode = 'VX' . $vipCounter++;
                } else {
                    $seatCode = $rowLetter . str_pad($col, 2, '0', STR_PAD_LEFT);
                }

                $seats[] = [
                    'seat_code' => $seatCode,
                    'type'      => $type,
                    'row'       => $row,
                    'col'       => $col,
                    'status'    => true,
                    'studio_id' => $studio->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Mass insert for efficiency
        Seat::insert($seats);
    }
}
