<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Observers\ActivityObserver;
use App\Http\Controllers\Controller;

class MovieController extends Controller
{
    protected ActivityObserver $activityObserver;

    public function __construct()
    {
        $this->activityObserver = new ActivityObserver();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Movie::query()->latest('updated_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $movies = $query->paginate(15)->withQueryString();

        return spaRender($request, 'movies.index', [
            'movies' => $movies
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return spaRender($request, 'movies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'required|array|min:1',
            'cast' => 'required|array|min:1',
            'director' => 'required|string|max:255',
            'release_date' => 'required|string',
            'duration' => ['required', 'regex:/^\d{1,2}:\d{2}$/'],
            'poster' => 'required|file|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $movie = Movie::create([
            'title' => $request->title,
            'description' => $request->description ?? null,
            'genre' => $request->genre,
            'cast' => $request->cast,
            'director' => $request->director,
            'release_date' => $request->release_date,
            'duration' => $request->duration,
            'poster' => $request->file('poster'),
            'status' => 'coming_soon',
        ]);

        $this->activityObserver->logCustom(
            message: 'Film baru ditambahkan: ' . $request->title,
            event: 'created',
            logName: 'movies',
            properties: [
                'movie_id' => $movie->id,
                'title' => $request->title,
                'director' => $request->director,
            ],
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Film berhasil ditambahkan.',
            'redirect' => route('movies.index'),
            'redirect_type' => 'spa',
        ]);
    }

    /**
     * Display the specified resource.
     * 
     */
    public function show(Request $request, Movie $movie)
    {
        $data = [
            'movie' => $movie,
        ];

        return spaRender($request, 'movies.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Movie $movie)
    {
        $data = [
            'movie' => $movie,
        ];

        return spaRender($request, 'movies.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'required|array|min:1',
            'cast' => 'required|array|min:1',
            'director' => 'required|string|max:255',
            'release_date' => 'required|string',
            'duration' => ['required', 'regex:/^\d{1,2}:\d{2}$/'],
            'status' => 'required|in:coming_soon,now_showing,ended',
            'poster' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        return DB::transaction(function () use ($request, $movie) {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'genre' => $request->genre,
                'cast' => $request->cast,
                'director' => $request->director,
                'release_date' => $request->release_date,
                'duration' => $request->duration,
                'status' => $request->status,
            ];

            if ($request->hasFile('poster')) {
                $updateData['poster'] = $request->file('poster');
            }

            $movie->update($updateData);

            $this->activityObserver->logCustom(
                message: 'Film diperbarui: ' . $movie->title,
                event: 'updated',
                logName: 'movies',
                properties: [
                    'movie_id' => $movie->id,
                    'title' => $movie->title,
                    'changes' => array_keys($updateData),
                ],
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Film berhasil diperbarui.',
                'redirect' => route('movies.show', $movie->slug),
                'redirect_type' => 'http',
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        $movieData = ['movie_id' => $movie->id, 'title' => $movie->title];
        $movie->delete();

        $this->activityObserver->logCustom(
            message: 'Film dihapus: ' . $movieData['title'],
            event: 'deleted',
            logName: 'movies',
            properties: $movieData,
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Film berhasil dihapus.',
            'redirect' => route('movies.index'),
            'redirect_type' => 'http',
        ]);
    }
}
