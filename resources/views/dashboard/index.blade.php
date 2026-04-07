@extends('_layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Film</h6>
                        <h3 class="mb-0 fw-bold">{{ $total_movies }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                        <i class="fas fa-film fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Sedang Tayang</h6>
                        <h3 class="mb-0 fw-bold">{{ $active_movies }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded p-3">
                        <i class="fas fa-play-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Studio</h6>
                        <h3 class="mb-0 fw-bold">{{ $total_studios }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="fas fa-door-open fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Operator</h6>
                        <h3 class="mb-0 fw-bold">{{ $total_operators }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Film Baru Ditambahkan</h5>
                    <a href="{{ route('movies.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Poster</th>
                                    <th>Judul Film</th>
                                    <th>Rilis</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_movies as $movie)
                                    <tr>
                                        <td>
                                            <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" class="rounded" style="width: 40px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <p class="mb-0 fw-semibold">{{ $movie->title }}</p>
                                            <small class="text-muted">{{ Str::limit(implode(', ', (array) $movie->genre), 30) }}</small>
                                        </td>
                                        <td>{{ $movie->release_date }}</td>
                                        <td>{{ $movie->duration }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movie->statusColor }} bg-opacity-10 text-{{ $movie->statusColor }} px-3 py-2 rounded-pill">
                                                {{ $movie->statusLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data film.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Status Studio</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($studios as $studio)
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ $studio->name }}</h6>
                                    <small class="text-muted"><i class="fas fa-chair me-1"></i> {{ $studio->capacity }} Kursi</small>
                                </div>
                                <span class="badge bg-{{ $studio->statusColor }} px-3 py-2 rounded-pill">
                                    {{ $studio->statusLabel }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-center py-4 text-muted">
                                Belum ada data studio.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
