<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Studio;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data statistik utama
        $data = [
            'total_movies'    => Movie::count(),
            'active_movies'   => Movie::where('status', 'now_showing')->count(),
            'total_studios'   => Studio::count(),
            // Asumsi role untuk operator bernama 'operator'
            'total_operators' => User::whereHas('role', function ($query) {
                $query->where('name', 'operator');
            })->count(),

            // Mengambil 5 film terakhir yang ditambahkan
            'recent_movies'   => Movie::latest()->take(5)->get(),

            // Mengambil 5 studio beserta kapasitas totalnya
            'studios'         => Studio::take(5)->get(),
        ];

        return spaRender($request, 'dashboard.index', $data);
    }
}
