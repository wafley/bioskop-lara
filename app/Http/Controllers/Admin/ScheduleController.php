<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Studio;
use App\Models\Movie;

class ScheduleController extends Controller
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));

        $schedules = Schedule::with(['movie', 'studio'])
            ->where('show_date', $date)
            ->orderBy('start_time', 'asc')
            ->get();

        $data = [
            'schedules' => $schedules,
            'date'      => $date,
        ];

        return spaRender($request, 'schedules.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $movies = Movie::where('status', 'now_showing')->get();
        $studios = Studio::where('status', true)->get();

        $data = [
            'movies'  => $movies,
            'studios' => $studios,
        ];

        return spaRender($request, 'schedules.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'show_date'  => 'required|string',
            'movie_id'   => 'required|exists:movies,id',
            'studio_id'  => 'required|exists:studios,id',
            'start_time' => 'required|string',
            'price'      => 'required|numeric',
        ]);

        try {
            $this->scheduleService->createSchedule($validated);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Jadwal berhasil ditambahkan.',
                'redirect'  => route('schedules.index'),
                'redirect_type' => 'spa',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Schedule $schedule)
    {
        $schedule = $schedule->load(['movie', 'studio.seats']);
        $seats = $schedule->studio->seats->groupBy('row');
        $data = [
            'schedule' => $schedule,
            'seats'    => $seats,
        ];

        return spaRender($request, 'schedules.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Schedule $schedule)
    {
        $movies = Movie::where('status', 'now_showing')->get();
        $studios = Studio::where('status', true)->get();

        $schedule->show_date = \Carbon\Carbon::parse($schedule->show_date)->format('d-m-Y');

        $data = [
            'movies'    => $movies,
            'studios'   => $studios,
            'schedule'  => $schedule
        ];

        return spaRender($request, 'schedules.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'show_date'  => 'required|string',
            'movie_id'   => 'required|exists:movies,id',
            'studio_id'  => 'required|exists:studios,id',
            'start_time' => 'required|string',
            'price'      => 'required|numeric',
        ]);

        try {
            $this->scheduleService->updateSchedule($schedule, $validated);
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil diperbarui.',
                'redirect'  => route('schedules.show', $schedule->uuid),
                'redirect_type' => 'spa',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil dihapus.',
                'redirect' => route('schedules.index'),
                'redirect_type' => 'http',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
