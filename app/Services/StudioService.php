<?php

namespace App\Services;

use App\Models\Seat;
use App\Models\Studio;
use App\View\Components\Seats;
use Illuminate\Support\Facades\DB;

class StudioService
{
    /**
     * Creating new studio with seat generation in a transaction.
     * @param array $data
     * @param bool $generateVip
     * @return bool
     * @throws \Exception
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
     * Update existing studio details.
     *
     * @param Studio $studio
     * @param array $data
     * @return bool
     */
    public function updateStudio(Studio $studio, array $data): bool
    {
        try {
            return $studio->update($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a studio and its related seats.
     *
     * @param Studio $studio
     * @return bool
     */
    public function deleteStudio(Studio $studio): bool
    {
        try {
            return (bool) $studio->delete();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Render a preview of the seat layout for a given studio configuration.
     *
     * @param int $rows
     * @param int $cols
     * @param bool $generateVip
     * @return array
     * @throws \Exception
     */
    public function renderSeatsPreview(int $rows, int $cols, bool $generateVip): array
    {
        $seatsComponent = new Seats(null, $rows, $cols, false, $generateVip);

        $html = view('components.seats', [
            'seats' => $seatsComponent->seats,
            'isEdit' => false
        ])->render();

        // Check if there are VIP seats in the generated results
        $hasVip = false;
        foreach ($seatsComponent->seats as $row) {
            foreach ($row as $seat) {
                if ($seat->type === 'vip') {
                    $hasVip = true;
                    break 2;
                }
            }
        }

        return [
            'html'    => $html,
            'total'   => $rows * $cols,
            'size'    => "$rows x $cols",
            'has_vip' => $hasVip
        ];
    }

    /**
     * Generate VIP seats for an existing studio.
     *
     * @param Studio $studio
     * @return bool
     * @throws \Exception
     */
    public function generateVipSeats(Studio $studio): bool
    {
        return DB::transaction(function () use ($studio) {
            $rows = $studio->rows;
            $cols = $studio->cols;

            $sideSection = floor($cols / 4);
            $centerSection = $cols - ($sideSection * 2);
            $center_start = $sideSection + 1;
            $center_end   = $center_start + $centerSection - 1;

            $vipRow = (int) ceil($rows / 2);
            $vipCounter = 1;

            // Look for seats in the middle row and middle section
            $seatsToUpdate = $studio->seats()
                ->where('row', $vipRow)
                ->whereBetween('col', [$center_start, $center_end])
                ->get();

            foreach ($seatsToUpdate as $seat) {
                $seat->update([
                    'type' => 'vip',
                    'seat_code' => 'VX' . $vipCounter++
                ]);
            }

            return true;
        });
    }

    /**
     * Generate seats for a studio based on its configuration.
     *
     * @param Studio $studio
     * @param bool $generateVip
     * @return void
     * @throws \Exception
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
