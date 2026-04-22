<?php

namespace App\Http\Controllers\Admin;

use App\Models\Movie;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return spaRender($request, 'movies.index');
    }

    public function data(Request $request)
    {
        $movies = Movie::orderBy('updated_at', 'desc');

        if ($request->status !== null && $request->status !== '') {
            $movies->where('status', $request->status);
        }

        return DataTables::of($movies)
            ->addIndexColumn()
            ->editColumn('poster', function ($row) {
                return "<img src='{$row->poster}' alt='{$row->title} Poster' class='img-thumbnail' width='80'>";
            })
            ->editColumn('duration', function ($row) {
                return $row->duration ?? '-';
            })
            ->editColumn('genre', function ($row) {
                return "<i class='bi bi-tags me-1'></i>" . implode(', ', $row->genre ?? []);
            })
            ->editColumn('cast', function ($row) {
                return implode(', ', $row->cast ?? []);
            })
            ->editColumn('status', function ($row) {
                return "<span class='badge text-bg-{$row->status_color}'>{$row->status_label}</span>";
            })
            ->addColumn('action', function ($row) {
                $detailUrl = route('movies.show', $row->slug);
                $editUrl = route('movies.edit', $row->slug);
                $deleteUrl = route('movies.destroy', $row->slug);

                return "
                    <a href='{$detailUrl}' class='btn btn-sm btn-primary spa-link'>Detail <i class='bi bi-arrow-right'></i></a>
                    <a href='{$editUrl}' class='btn btn-sm btn-info spa-link'><i class='bi bi-pencil-square'></i></a>
                    <button class='btn btn-sm btn-danger' data-ajax='delete' data-url='{$deleteUrl}'><i class='bi bi-trash'></i></button>
                ";
            })
            ->rawColumns(['poster', 'genre', 'status', 'action'])
            ->make();
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

        Movie::create([
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
        $movie->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Film berhasil dihapus.',
            'redirect' => route('movies.index'),
            'redirect_type' => 'http',
        ]);
    }
}
