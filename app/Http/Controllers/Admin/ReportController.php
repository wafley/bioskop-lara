<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $queryStartDate = Carbon::parse($startDate)->startOfDay();
        $queryEndDate   = Carbon::parse($endDate)->endOfDay();

        $baseQuery = Transaction::query()
            ->whereBetween('created_at', [$queryStartDate, $queryEndDate])
            ->where('status', 'success');

        // 1. Agregat Utama
        $aggregates = (clone $baseQuery)
            ->selectRaw('
            COALESCE(SUM(total_price), 0) as total_revenue,
            COUNT(id) as total_transactions,
            COALESCE(SUM(total_tickets), 0) as total_tickets
        ')->first();

        // 2. Data Harian untuk Grafik Utama
        $dailyTransactions = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(total_price) as revenue')
            ->selectRaw('SUM(total_tickets) as tickets')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dailyData = [];
        $currentDate = $queryStartDate->copy();
        while ($currentDate <= $queryEndDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dailyData[] = [
                'date' => $dateString,
                'revenue' => isset($dailyTransactions[$dateString]) ? (float) $dailyTransactions[$dateString]->revenue : 0,
                'tickets' => isset($dailyTransactions[$dateString]) ? (int) $dailyTransactions[$dateString]->tickets : 0,
            ];
            $currentDate->addDay();
        }

        // 3. Data Top 5 Film Terlaris
        $topMovies = DB::table('tickets')
            ->join('transactions', 'tickets.transaction_id', '=', 'transactions.id')
            ->join('schedules', 'tickets.schedule_id', '=', 'schedules.id')
            ->join('movies', 'schedules.movie_id', '=', 'movies.id')
            ->whereBetween('transactions.created_at', [$queryStartDate, $queryEndDate])
            ->where('transactions.status', 'success')
            ->whereIn('tickets.status', ['active', 'used'])
            ->selectRaw('movies.title, COUNT(tickets.id) as tickets_sold, SUM(tickets.price) as revenue')
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // 4. Data Distribusi Metode Pembayaran
        $paymentMethods = (clone $baseQuery)
            ->selectRaw('payment_method, COUNT(id) as count, SUM(total_price) as revenue')
            ->groupBy('payment_method')
            ->get();

        return spaRender($request, 'reports.index', [
            'start_date'          => $startDate,
            'end_date'            => $endDate,
            'total_revenue'       => (float) $aggregates->total_revenue,
            'total_transactions'  => (int) $aggregates->total_transactions,
            'total_tickets'       => (int) $aggregates->total_tickets,
            'daily_data'          => $dailyData,
            'top_movies'          => $topMovies,
            'payment_methods'     => $paymentMethods,
        ]);
    }
}
