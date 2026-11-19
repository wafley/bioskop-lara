<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use App\Models\Studio;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $schedules = Schedule::select('start_time', 'end_time')
            ->distinct()
            ->orderBy('start_time')
            ->get();

        $timeSlots = $schedules->map(function ($schedule) {
            return Carbon::parse($schedule->start_time)->format('H:i') . ' - ' . Carbon::parse($schedule->end_time)->format('H:i');
        })->unique()->values();

        $transactions = Transaction::query()
            ->select('schedules.start_time', 'schedules.end_time', DB::raw('COUNT(transactions.id) as total_transactions'))
            ->join('schedules', 'transactions.schedule_id', '=', 'schedules.id')
            ->where('transactions.status', 'success')
            ->groupBy('schedules.start_time', 'schedules.end_time')
            ->get();

        $peakHoursData = [];
        foreach ($transactions as $t) {
            $slot = Carbon::parse($t->start_time)->format('H:i') . ' - ' . Carbon::parse($t->end_time)->format('H:i');
            $peakHoursData[$slot] = ($peakHoursData[$slot] ?? 0) + (int) $t->total_transactions;
        }

        $peakHours = [];
        foreach ($timeSlots as $slot) {
            $peakHours[] = [
                'hour' => $slot,
                'total_transactions' => $peakHoursData[$slot] ?? 0
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
