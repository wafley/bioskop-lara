<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\View\Components\Seats;
use App\Http\Controllers\Controller;

class SeatsController extends Controller
{
    public function render(Request $request)
    {
        $rows = (int) $request->rows;
        $cols = (int) $request->cols;
        $generateVip = filter_var($request->generate_vip ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($rows <= 0 || $cols <= 0) {
            return response()->json([
                'html' => '<p class="text-muted text-center">Masukkan jumlah baris & kolom untuk menampilkan preview kursi.</p>',
                'total' => 0,
                'size' => "0 x 0",
            ]);
        }

        $seatsComponent = new Seats(null, $rows, $cols, false, $generateVip);
        $html = view('components.seats', [
            'seats' => $seatsComponent->seats,
            'isEdit' => false
        ])->render();

        return response()->json([
            'html' => $html,
            'total' => $rows * $cols,
            'size' => "$rows x $cols",
        ]);
    }
}
