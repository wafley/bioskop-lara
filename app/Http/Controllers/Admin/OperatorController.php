<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OperatorController extends Controller
{
    protected Role $role;

    public function __construct()
    {
        $this->role = Role::where('name', 'operator')->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'operators.index');
    }

    public function data(Request $request)
    {
        $operators = User::with('role')->where('role_id', $this->role->id)->orderBy('updated_at', 'desc');

        if ($request->status !== null && $request->status !== '') {
            $operators->where('status', $request->status);
        }

        return DataTables::of($operators)
            ->addIndexColumn()
            ->editColumn('created_at', fn($row) => formatDate($row->created_at))
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('operators.show', $row->username);
                $editUrl = route('operators.edit', $row->username);
                $deleteUrl = route('operators.destroy', $row->username);

                return "
                    <a href='{$detailUrl}' class='btn btn-sm btn-primary spa-link'>Detail <i class='bi bi-arrow-right'></i></a>
                    <a href='{$editUrl}' class='btn btn-sm btn-info spa-link'><i class='bi bi-pencil-square'></i></a>
                    <button class='btn btn-sm btn-danger' data-ajax='delete' data-url='{$deleteUrl}'><i class='bi bi-trash'></i></button>
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
        return spaRender($request, 'operators.create');
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
            'message' => 'Operator berhasil ditambahkan.',
            'redirect' => route('operators.index'),
            'redirect_type' => 'spa',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $operator)
    {
        $activities = $operator->activities()->latest()->take(10)->get();

        $data = [
            'operator' => $operator,
            'activities' => $activities,
        ];

        return spaRender($request, 'operators.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $operator)
    {
        $data = [
            'operator' => $operator,
        ];

        return spaRender($request, 'operators.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $operator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $operator->id,
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'boolean',
        ]);

        return DB::transaction(function () use ($request, $operator) {
            $operator->update([
                'name' => $request->name,
                'username' => $request->username,
                'status' => $request->boolean('status'),
                ...(!empty($request->password) ? ['password' => $request->password] : []),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Operator berhasil diperbarui.',
                'redirect' => route('operators.show', $operator->username),
                'redirect_type' => 'http',
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $operator)
    {
        $operator->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Operator berhasil dihapus.',
            'redirect' => route('operators.index'),
            'redirect_type' => 'http',
        ]);
    }
}
