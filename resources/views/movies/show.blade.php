@extends('_layouts.app')
@section('title', $movie->title)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="card custom-card">
                <div class="card-body">
                    <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="img-fluid rounded border">
                    <p class="text-muted mb-0 mt-3">Dirilis pada {{ $movie->release_date }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h4 class="card-title">Informasi Film</h4>
                            <small class="text-muted w-100">
                                Terakhir diperbarui {{ formatDate($movie->updated_at) }}
                            </small>
                        </div>

                        @role('admin')
                            <div class="d-flex align-items-center gap-3">
                                <a href="{{ route('movies.edit', $movie->slug) }}" class="btn btn-info spa-link">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <button class="btn btn-danger" data-ajax="delete" data-url="{{ route('movies.destroy', $movie->slug) }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        @endrole
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="text-uppercase fw-bold text-primary mb-0">
                            {{ $movie->title }}
                        </h3>

                        <span class='badge text-bg-{{ $movie->status_color }}'>{{ $movie->status_label }}</span>
                    </div>

                    <div class="d-flex gap-2 align-items-center mb-3">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            {{ $movie->duration }}
                        </small>

                        <small class="text-muted">
                            <i class="bi bi-tags me-1"></i>
                            {{ implode(', ', $movie->genre) }}
                        </small>
                    </div>

                    <p>{{ $movie->description }}</p>

                    <ul class="list-unstyled">
                        <li>
                            <strong>Sutradara:</strong>
                            <span class="fst-italic">{{ $movie->director }}</span>
                        </li>
                        <li>
                            <strong>Pemeran:</strong>
                            @foreach ($movie->cast as $cast)
                                <span class='badge text-bg-secondary'>{{ $cast }}</span>
                            @endforeach
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
