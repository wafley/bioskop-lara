<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Seats extends Component
{
    public $seats, $rows, $cols, $isEdit, $generateVip;

    /**
     * Create a new component instance.
     */
    public function __construct($seats = null, $rows = null, $cols = null, $isEdit = false, $generateVip = false)
    {
        $this->rows = max(0, (int) $rows);
        $this->cols = max(0, (int) $cols);
        $this->isEdit = filter_var($isEdit, FILTER_VALIDATE_BOOLEAN);
        $this->generateVip = filter_var($generateVip, FILTER_VALIDATE_BOOLEAN);

        $this->seats = $seats ?? $this->generateSeats();
    }

    protected function generateSeats(): array
    {
        $seats = [];
        $sideSection = floor($this->cols / 4);
        $centerSection = $this->cols - $sideSection * 2;
        $center_start = $sideSection + 1;
        $center_end = $center_start + $centerSection - 1;
        $vipRow = $this->rows > 0 ? (int) ceil($this->rows / 2) : null;
        $vipCounter = 1;

        for ($r = 1; $r <= $this->rows; $r++) {
            $rowLetter = chr(64 + $r);
            $seats[$r] = [];

            // Section A
            for ($c = 1; $c <= $sideSection; $c++) {
                $seats[$r][] = (object)[
                    'type' => 'regular',
                    'status' => true,
                    'seat_code' => $rowLetter . str_pad($c, 2, '0', STR_PAD_LEFT),
                    'section' => 'A',
                ];
            }

            // Section B
            for ($c = $center_start; $c <= $center_end; $c++) {
                $type = 'regular';
                $seat_code = $rowLetter . str_pad($c, 2, '0', STR_PAD_LEFT);
                if ($this->generateVip && $r === $vipRow) {
                    $type = 'vip';
                    $seat_code = 'VX' . $vipCounter++;
                }

                $seats[$r][] = (object)[
                    'type' => $type,
                    'status' => true,
                    'seat_code' => $seat_code,
                    'section' => 'B',
                ];
            }

            // Section C
            for ($c = $center_end + 1; $c <= $this->cols; $c++) {
                $seats[$r][] = (object)[
                    'type' => 'regular',
                    'status' => true,
                    'seat_code' => $rowLetter . str_pad($c, 2, '0', STR_PAD_LEFT),
                    'section' => 'C',
                ];
            }
        }

        return $seats;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.seats', [
            'seats' => $this->seats,
            'isEdit' => $this->isEdit,
        ]);
    }
}
