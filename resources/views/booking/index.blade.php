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

                            <div class="col-12">
                                <div class="card custom-card border shadow-sm shadow-hover">
                                    <div class="row g-0">
                                        <div class="col-md-2">
                                            <img src="{{ $movie->poster }}" class="img-fluid rounded-start" alt="{{ $movie->title }} Poster">
                                        </div>
                                        <div class="col-md-10">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h5 class="card-title fs-4 text-primary mb-0">
                                                        {{ $movie->title }}
                                                    </h5>

                                                    <span class="badge rounded-pill bg-info ms-2">
                                                        {{ $movieSchedules->count() }} Jadwal Tayang
                                                    </span>
                                                </div>

                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i> {{ $movie->duration }}
                                                </small>

                                                <p class="card-text mt-3 mb-1">
                                                    <strong>Jadwal Tayang:</strong>
                                                </p>

                                                <div class="d-flex flex-wrap gap-3 mb-3">
                                                    @foreach ($sortedSchedules as $sch)
                                                        <a href="{{ route('booking.show', $sch->uuid) }}" class="text-decoration-none spa-link">
                                                            <div class="card custom-card border">
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        {{-- Icon --}}
                                                                        <div class="bg-info-gradient text-white rounded-circle py-2 px-3">
                                                                            <i class="bi bi-camera-reels fs-4"></i>
                                                                        </div>

                                                                        {{-- Info --}}
                                                                        <div>
                                                                            <h5 class="fw-bold text-dark mb-0">
                                                                                {{ $sch->studio->name }}
                                                                            </h5>

                                                                            <small class="small text-muted mb-0">
                                                                                Kapasitas: {{ $sch->studio->capacity }} Kursi
                                                                            </small>
                                                                        </div>
                                                                    </div>

                                                                    <hr>

                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <span class="fw-semibold text-muted fs-6">
                                                                            {{ substr($sch->start_time, 0, 5) }}
                                                                        </span>

                                                                        <div class="badge text-bg-secondary rounded-circle py-2 px-2">
                                                                            <i class="fa fa-arrow-right"></i>
                                                                        </div>

                                                                        <span class="fw-semibold text-muted fs-6">
                                                                            {{ substr($sch->end_time, 0, 5) }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="card-footer bg-light-gradient">
                                                                    <h4 class="mb-0 fw-bold text-success">
                                                                        {{ formatPrice($sch->price) }}
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>

                                                {{-- <div class="list-group">
                                                    @foreach ($sortedSchedules as $sch)
                                                        <a href="{{ route('booking.show', $sch->uuid) }}" class="list-group-item list-group-item-action spa-link">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div>
                                                                    <span class="fs-6 fw-bold">
                                                                        {{ $sch->studio->name }}
                                                                    </span>
                                                                    |
                                                                    <small class="badge text-bg-secondary">
                                                                        {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                                                    </small>
                                                                </div>
                                                                <h6 class="fs-6 fw-bold text-primary">
                                                                    {{ formatPrice($sch->price) }}
                                                                </h6>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div> --}}
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
