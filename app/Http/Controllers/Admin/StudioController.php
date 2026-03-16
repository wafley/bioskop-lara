<?php

namespace App\Http\Controllers\Admin;

use App\Models\Studio;
use Illuminate\Http\Request;
use App\Services\StudioService;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class StudioController extends Controller
{
    protected StudioService $studioService;

    public function __construct(StudioService $studioService)
    {
        $this->studioService = $studioService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'studios.index');
    }

    public function data(Request $request)
    {
        $studios = Studio::orderBy('updated_at', 'desc');

        if ($request->status !== null && $request->status !== '') {
            $studios->where('status', $request->status);
        }

        return DataTables::of($studios)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('studios.show', $row->slug);
                $editUrl = route('studios.edit', $row->slug);
                $deleteUrl = route('studios.destroy', $row->slug);

                return "
                    <a href='{$detailUrl}' class='btn btn-sm btn-primary spa-link'>Detail <i class='bi bi-arrow-right'></i></a>
                    <a href='{$editUrl}' class='btn btn-sm btn-info spa-link'><i class='bi bi-pencil-square'></i></a>
                    <button class='btn btn-sm btn-danger' data-ajax='delete' data-url='{$deleteUrl}'><i class='bi bi-trash'></i></button>
                ";
            })
            ->rawColumns(['status', 'action'])
            ->make();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return spaRender($request, 'studios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rows' => 'required|integer|min:1',
            'cols' => 'required|integer|min:1',
        ]);

        $generateVip = $request->has('generate_vip');

        if ($this->studioService->createStudio($validated, $generateVip)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Studio berhasil ditambahkan!',
                'redirect' => route('studios.index'),
                'redirect_type' => 'spa',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat menyimpan data.'
        ], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Studio $studio)
    {
        $studio->load('seats');

        $seats = $studio->seats->groupBy('row');

        $seat_types = (object) [
            'regular' => $studio->seats->where('type', 'regular')->count(),
            'vip' => $studio->seats->where('type', 'vip')->count(),
            'disabled' => $studio->seats->where('type', 'disabled')->count(),
        ];

        $data = [
            'studio' => $studio,
            'seats' => $seats,
            'seat_types' => $seat_types,
            'rows' => $studio->rows,
            'cols' => $studio->cols,
        ];

        return spaRender($request, 'studios.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Studio $studio)
    {
        $data = [
            'studio' => $studio,
        ];

        return spaRender($request, 'studios.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Studio $studio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Studio $studio)
    {
        //
    }
}
