<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Movie;
use App\Models\Transaction;
use Illuminate\Http\Request;
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

        $aggregates = (clone $baseQuery)
            ->selectRaw('
            COALESCE(SUM(total_price), 0) as total_revenue,
            COUNT(id) as total_transactions,
            COALESCE(SUM(total_tickets), 0) as total_tickets
        ')->first();

        $dailyTransactions = Transaction::query()
            ->whereBetween('created_at', [$queryStartDate, $queryEndDate])
            ->where('status', 'success')
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

        return spaRender($request, 'reports.index', [
            'start_date'          => $startDate,
            'end_date'            => $endDate,
            'total_revenue'       => (float) $aggregates->total_revenue,
            'total_transactions'  => (int) $aggregates->total_transactions,
            'total_tickets'       => (int) $aggregates->total_tickets,
            'daily_data'          => $dailyData,
        ]);
    }
}
