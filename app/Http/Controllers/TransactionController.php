<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'transactions.index');
    }

    /**
     * Displays transaction data for DataTables.
     */
    public function data(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $role = $user->role;

        $transactions = Transaction::query();

        if ($request->status !== null && $request->status !== '') {
            $transactions->where('status', $request->status);
        }

        if ($request->payment_method !== null && $request->payment_method !== '') {
            $transactions->where('payment_method', $request->payment_method);
        }

        if ($role->name === 'cashier') {
            $cashierId = $user->id;
            $transactions = $transactions->where('user_id', $cashierId);
        }

        $transactions = $transactions->with('cashier');

        return DataTables::of($transactions)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value'] != '') {
                    $search = $request->search['value'];

                    $query->where('invoice_number', 'like', "%{$search}%");
                }
            })
            ->editColumn('cashier', fn($row) => $row->cashier?->name)
            ->editColumn('total_price', fn($row) => formatPrice($row->total_price))
            ->editColumn('created_at', fn($row) => formatDate($row->created_at))
            ->editColumn('payment_method', fn($row) => $row->payment_method_label)
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_label->class}'>{$row->status_label->text}</span>";
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('transactions.show', $row->invoice_number);

                return "
                    <a href='{$detailUrl}' class='btn btn-sm btn-primary w-100 spa-link'>Detail <i class='bi bi-arrow-right'></i></a>
                ";
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Hub Page / Print Options
     */
    public function print(Transaction $transaction)
    {
        return view('transactions.print', compact('transaction'));
    }

    /**
     * Special Print Receipt / Payment Receipt
     */
    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['schedule.movie', 'schedule.studio', 'cashier']);
        return view('transactions.print-receipt', compact('transaction'));
    }

    /**
     * Special Printed Cinema Tickets (Per Seat)
     */
    public function printTicket(Transaction $transaction)
    {
        $transaction->load(['schedule.movie', 'schedule.studio', 'tickets.seat']);
        return view('transactions.print-ticket', compact('transaction'));
    }
}
