@extends('_layouts.app')
@section('title', $movie->title)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h4 class="card-title">Informasi Film</h4>
                            <small class="text-muted w-100">
                                Terakhir diperbarui {{ formatDate($movie->updated_at) }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('movies.edit', $movie->slug) }}" class="btn btn-info spa-link">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button class="btn btn-danger" data-ajax="delete" data-url="{{ route('movies.destroy', $movie->slug) }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="img-fluid rounded border">
                        </div>
                        <div class="col-lg-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="text-uppercase fw-bold text-primary">
                                    {{ $movie->title }}
                                </h3>
                                <p class="text-muted">Dirilis pada {{ $movie->release_date }}</p>
                            </div>

                            <p>
                                <strong>Durasi:</strong>
                                <span class="fst-italic fs-underline">{{ $movie->duration }}</span>
                            </p>

                            <p>{{ $movie->description }}</p>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Sutradara:</strong>
                                    <span class="fst-italic">{{ $movie->director }}</span>
                                </li>
                                <li class="list-group-item">
                                    <strong>Genre:</strong>
                                    @foreach ($movie->genre as $genre)
                                        <span class='badge text-bg-primary'>{{ $genre }}</span>
                                    @endforeach
                                </li>
                                <li class="list-group-item">
                                    <strong>Pemeran:</strong>
                                    @foreach ($movie->cast as $cast)
                                        <span class='badge text-bg-primary'>{{ $cast }}</span>
                                    @endforeach
                                </li>
                                <li class="list-group-item">
                                    <strong>Status:</strong>
                                    <span class='badge text-bg-{{ $movie->status_color }}'>{{ $movie->status_label }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
