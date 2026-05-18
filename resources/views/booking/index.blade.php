@extends('_layouts.app')
@section('title', 'Penjualan Tiket')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <form method="GET" action="{{ route('booking.index') }}">
                                <label class="form-label">Cari Film</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul film..." value="{{ request('search') }}">

                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        @forelse ($schedules->groupBy('movie_id') as $movieId => $movieSchedules)
                            @php
                                $movie = $movieSchedules->first()->movie;
                                $sortedSchedules = $movieSchedules->sortBy('start_time');
                            @endphp

                            <div class="col-md-6">
                                <div class="card custom-card border shadow-sm shadow-hover">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="{{ $movie->poster }}" class="img-fluid rounded-start" alt="{{ $movie->title }} Poster">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h5 class="card-title mb-0">
                                                        {{ $movie->title }}
                                                    </h5>

                                                    <span class="badge rounded-pill bg-info ms-2">
                                                        {{ $movieSchedules->count() }} Jadwal Tayang
                                                    </span>
                                                </div>

                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i> {{ $movie->duration }}
                                                </small>

                                                <p class="card-text mt-2 mb-1">
                                                    <strong>Jadwal Tayang:</strong>
                                                </p>

                                                <div class="list-group">
                                                    @foreach ($sortedSchedules as $sch)
                                                        <a href="" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center spa-link">
                                                            <div>
                                                                <div class="badge text-bg-secondary">
                                                                    {{ $sch->studio->name }}
                                                                </div>
                                                                {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                                            </div>
                                                            <h6 class="fw-bold text-primary">
                                                                {{ formatPrice($sch->price) }}
                                                            </h6>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card custom-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada jadwal tayang untuk tanggal ini.</h5>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
