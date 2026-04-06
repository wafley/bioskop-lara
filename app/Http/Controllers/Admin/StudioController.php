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
        $query = Studio::query()->latest('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $studios = $query->paginate(15)->withQueryString();

        return spaRender($request, 'studios.index', [
            'studios' => $studios
        ]);
    }

    /**
     * Method to handle AJAX Preview seats
     */
    public function renderSeats(Request $request)
    {
        $rows = (int) $request->rows;
        $cols = (int) $request->cols;
        $generateVip = filter_var($request->generate_vip ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($rows <= 0 || $cols <= 0) {
            return response()->json([
                'html' => '<p class="text-muted text-center">Masukkan jumlah baris & kolom untuk menampilkan preview kursi.</p>',
                'total' => 0,
                'size' => "0 x 0",
                'has_vip' => false
            ]);
        }

        $result = $this->studioService->renderSeatsPreview($rows, $cols, $generateVip);

        return response()->json($result);
    }

    /**
     * Method to add VIP to an existing studio
     */
    public function addVip(Studio $studio)
    {
        if ($this->studioService->generateVipSeats($studio)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kursi VIP berhasil di-generate!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal meng-generate kursi VIP.'
        ], 500);
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
        $studio->load('seats');
        $seats = $studio->seats->groupBy('row');

        $data = [
            'studio' => $studio,
            'seats'  => $seats,
        ];

        return spaRender($request, 'studios.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Studio $studio)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|boolean',
        ]);

        if ($this->studioService->updateStudio($studio, $validated)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Studio berhasil diperbarui!',
                'redirect' => route('studios.show', $studio->slug),
                'redirect_type' => 'spa',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memperbarui data.'
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Studio $studio)
    {
        if ($this->studioService->deleteStudio($studio)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Studio berhasil dihapus!',
                'redirect' => route('studios.index'),
                'redirect_type' => 'spa',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus data.'
        ], 500);
    }
}
