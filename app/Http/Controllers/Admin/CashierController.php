<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CashierController extends Controller
{
    protected Role $role;

    public function __construct()
    {
        $this->role = Role::where('name', 'cashier')->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'cashiers.index');
    }

    public function data(Request $request)
    {
        $cashiers = User::with('role')->where('role_id', $this->role->id)->orderByRaw('status = 1 DESC')->orderBy('updated_at', 'desc');

        if ($request->status !== null && $request->status !== '') {
            $cashiers->where('status', $request->status);
        }

        return DataTables::of($cashiers)
            ->addIndexColumn()
            ->editColumn('created_at', fn($row) => formatDate($row->created_at))
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('cashiers.show', $row->username);

                return "
                    <a href='{$detailUrl}' class='btn btn-sm btn-primary w-100 spa-link'>Detail <i class='bi bi-arrow-right'></i></a>
                ";
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return spaRender($request, 'cashiers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->password,
            'role_id' => $this->role->id,
            'status' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kasir baru telah berhasil ditambahkan.',
            'redirect' => route('cashiers.index'),
            'redirect_type' => 'spa',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $cashier)
    {
        $activities = $cashier->activities()->latest()->take(10)->get();

        $data = [
            'cashier' => $cashier,
            'activities' => $activities,
        ];

        return spaRender($request, 'cashiers.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $cashier)
    {
        $data = [
            'cashier' => $cashier,
        ];

        return spaRender($request, 'cashiers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $cashier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $cashier->id,
            'status' => 'boolean',
        ]);

        return DB::transaction(function () use ($request, $cashier) {
            $cashier->update([
                'name' => $request->name,
                'username' => $request->username,
                'status' => $request->boolean('status'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'cashier berhasil diperbarui.',
                'redirect' => route('cashiers.show', $cashier->username),
                'redirect_type' => 'spa',
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $cashier)
    {
        $cashier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kasir berhasil dihapus.',
            'redirect' => route('cashiers.index'),
            'redirect_type' => 'http',
        ]);
    }
}
