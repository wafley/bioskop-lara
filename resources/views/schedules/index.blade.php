@extends('_layouts.app')
@section('title', 'Schedule')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('styles')
    {{--  --}}
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="d-flex gap-3">
                <input type="date" class="form-control" value="{{ $date }}" onchange="window.location.href='?date='+this.value">
                <a href="{{ route('schedules.create') }}" class="btn btn-primary text-nowrap spa-link">
                    <i class="fs-6 me-2 bi bi-plus"></i>
                    Tambah Jadwal
                </a>
            </div>
        </div>
    </div>

    @forelse ($schedules->groupBy('movie_id') as $movieId => $movieSchedules)
        @php $movie = $movieSchedules->first()->movie; @endphp
        <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2 text-center">
                                @if ($movie->poster)
                                    <img src="{{ $movie->poster }}" class="rounded shadow-sm border" alt="{{ $movie->title }}" width="200">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center movie-poster border">
                                        <i class="bi bi-film fs-1 text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-10 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ $movie->title }}</h4>
                                        <div class="d-flex gap-2 align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $movie->duration }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-tags me-1"></i>
                                                {{ implode(', ', $movie->genre) }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-start">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('movies.show', $movie->slug) }}">
                                                    Rincian
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <hr class="my-3 opacity-25">

                                <div class="row flex-grow-1">
                                    @foreach ($movieSchedules->groupBy('studio_id') as $studioId => $times)
                                        @php $studio = $times->first()->studio; @endphp
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <a href="{{ route('studios.show', $studio->slug) }}" class="d-block fw-bold text-primary mb-3">
                                                <i class="bi bi-door-open me-1"></i>
                                                {{ $studio->name }}
                                                <i class="small bi bi-arrow-up-right"></i>
                                            </a>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach ($times->sortBy('start_time') as $sch)
                                                    <a href="{{ route('schedules.show', $sch->uuid) }}" class="btn btn-light shadow-sm spa-link text-center px-3">
                                                        <div class="badge bg-{{ $sch->status_label->class }} mb-1">
                                                            <i class="bi {{ $sch->status_label->icon }}"></i>
                                                            {{ $sch->status_label->text }}
                                                        </div>

                                                        <hr class="my-1 opacity-25">

                                                        <div class="fw-bold">
                                                            {{ substr($sch->start_time, 0, 5) }}
                                                        </div>

                                                        <i class="bi bi-arrow-down-short d-block"></i>

                                                        <div class="fw-bold text-muted">
                                                            {{ substr($sch->end_time, 0, 5) }}
                                                        </div>

                                                        <hr class="my-1 opacity-25">

                                                        <div class="text-primary fw-bold">
                                                            {{ formatPrice($sch->price) }}
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-auto">
                                    <small class="text-muted fst-italic">* Klik pada jam tayang untuk melihat detail jadwal.</small>
                                </div>
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
                <a href="{{ route('schedules.create') }}" class="btn btn-primary mt-2 spa-link">Buat Jadwal Baru</a>
            </div>
        </div>
    @endforelse
@endsection
