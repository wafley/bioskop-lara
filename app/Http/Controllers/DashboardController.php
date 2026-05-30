<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Studio;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $role = $user->role;

        if ($role->name === 'admin') {
            return $this->adminDashboard($request);
        } else if ($role->name === 'cashier') {
            return $this->cashierDashboard($request);
        }

        abort(403);
    }

    private function adminDashboard(Request $request)
    {
        $totalRevenue = Transaction::where('status', 'success')
            ->sum('total_price');

        $totalTransactions = Transaction::where('status', 'success')
            ->count();

        $activeMovies = Movie::where('status', 'now_showing')->count();

        $activeCashiers = User::whereHas('role', function ($query) {
            $query->where('name', 'cashier');
        })->where('status', true)->count();

        $popularMovies = Movie::query()
            ->select('movies.id', 'movies.title')
            ->selectRaw('COALESCE(SUM(transactions.total_tickets), 0) as total_tickets')
            ->leftJoin('schedules', 'movies.id', '=', 'schedules.movie_id')
            ->leftJoin('transactions', function ($join) {
                $join->on('schedules.id', '=', 'transactions.schedule_id')
                    ->where('transactions.status', 'success');
            })
            ->where('movies.status', 'now_showing')
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('total_tickets')
            ->take(5)
            ->get();

        $topCashiers = User::query()
            ->select('users.id', 'users.name')
            ->selectRaw('COUNT(transactions.id) as total_transactions')
            ->selectRaw('SUM(transactions.total_price) as total_revenue')
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.status', 'success')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_transactions')
            ->take(10)
            ->get();

        $studioStats = Studio::query()
            ->select('studios.id', 'studios.name')
            ->selectRaw('COALESCE(SUM(transactions.total_tickets), 0) as total_tickets')
            ->leftJoin('schedules', 'studios.id', '=', 'schedules.studio_id')
            ->leftJoin('transactions', function ($join) {
                $join->on('schedules.id', '=', 'transactions.schedule_id')
                    ->where('transactions.status', 'success');
            })
            ->where('studios.status', true)
            ->groupBy('studios.id', 'studios.name')
            ->get();

        $recentTransactions = Transaction::with(['cashier', 'schedule.movie'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $timeSlots = Schedule::selectRaw("CONCAT(TIME_FORMAT(start_time, '%H:%i'), ' - ', TIME_FORMAT(end_time, '%H:%i')) as time_slot")
            ->distinct()
            ->orderBy('start_time')
            ->pluck('time_slot');

        $peakHoursData = Transaction::query()
            ->selectRaw("CONCAT(TIME_FORMAT(schedules.start_time, '%H:%i'), ' - ', TIME_FORMAT(schedules.end_time, '%H:%i')) as time_slot")
            ->selectRaw('COUNT(transactions.id) as total_transactions')
            ->join('schedules', 'transactions.schedule_id', '=', 'schedules.id')
            ->where('transactions.status', 'success')
            ->groupBy('time_slot')
            ->get()
            ->keyBy('time_slot');

        $peakHours = [];
        foreach ($timeSlots as $slot) {
            $peakHours[] = [
                'hour' => $slot,
                'total_transactions' => isset($peakHoursData[$slot]) ? $peakHoursData[$slot]->total_transactions : 0
            ];
        }

        return spaRender($request, 'dashboard.admin', compact(
            'totalRevenue',
            'totalTransactions',
            'activeMovies',
            'activeCashiers',
            'popularMovies',
            'topCashiers',
            'studioStats',
            'recentTransactions',
            'peakHours'
        ));
    }

    private function cashierDashboard(Request $request)
    {
        $userId = Auth::id();
        $today = \Carbon\Carbon::today();

        $baseQuery = Transaction::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->where('status', 'success');

        $todayRevenue = (clone $baseQuery)->sum('total_price');
        $todayTickets = (clone $baseQuery)->sum('total_tickets');
        $todayTransactions = (clone $baseQuery)->count();

        $recentTransactions = Transaction::with(['schedule.movie'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $todaySchedules = Movie::whereHas('schedules', function ($query) use ($today) {
                $query->where('show_date', $today->format('Y-m-d'));
            })
            ->with(['schedules' => function ($query) use ($today) {
                $query->where('show_date', $today->format('Y-m-d'))
                      ->with('studio')
                      ->orderBy('start_time');
            }])
            ->get();

        return spaRender($request, 'dashboard.cashier', compact(
            'todayRevenue',
            'todayTickets',
            'todayTransactions',
            'recentTransactions',
            'todaySchedules'
        ));
    }
}
