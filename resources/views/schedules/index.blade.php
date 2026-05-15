@extends('_layouts.app')
@section('title', 'Schedule')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('styles')
    <style>
        .hover-gap:hover {
            background-color: rgba(13, 110, 253, 0.1) !important;
            border: 1px dashed #0d6efd;
        }

        .hover-opacity-100 {
            transition: opacity 0.2s;
        }

        .hover-gap:hover .hover-opacity-100 {
            opacity: 1 !important;
        }

        .btn-outline-dashed {
            border: 1px dashed #dee2e6;
        }

        .timeline-container {
            overflow: hidden;
        }
    </style>
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

    @forelse ($schedules->groupBy('studio_id') as $studioId => $studioSchedules)
        @php
            $studio = $studioSchedules->first()->studio;
            $sortedSchedules = $studioSchedules->sortBy('start_time');
        @endphp

        <div class="card custom-card mb-4 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-door-open-fill me-1"></i> {{ $studio->name }}
                        </h5>
                        <small class="text-muted">Kapasitas: {{ $studio->capacity }} Kursi</small>
                    </div>

                    <span class="badge rounded-pill bg-info px-3">
                        {{ $studioSchedules->count() }} Sesi Jadwal
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="vtimeline">
                    @foreach ($sortedSchedules as $index => $sch)
                        {{-- Gunakan class 'timeline-inverted' untuk posisi selang-seling --}}
                        <div class="timeline-wrapper {{ $index % 2 == 0 ? '' : 'timeline-inverted' }} timeline-wrapper-primary">
                            <div class="timeline-badge bg-primary shadow-sm"></div>

                            <div class="timeline-panel border shadow-none hover-shadow-sm transition-all">
                                <div class="timeline-heading">
                                    <div class="d-flex gap-3">
                                        <!-- Poster Mini -->
                                        <div class="flex-shrink-0">
                                            <img src="{{ $sch->movie->poster }}" class="rounded shadow-sm" style="width: 60px; height: 85px; object-fit: cover;"
                                                alt="{{ $sch->movie->title }}">
                                        </div>

                                        <!-- Informasi Film -->
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1 text-dark text-truncate" style="max-width: 200px;">
                                                {{ $sch->movie->title }}
                                            </h6>

                                            <div class="mb-2">
                                                <span class="badge bg-light text-primary border me-1">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                                </span>
                                                <span class="badge bg-{{ $sch->status_label->class }} small">
                                                    {{ $sch->status_label->text }}
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <span class="fw-bold text-success">{{ formatPrice($sch->price) }}</span>
                                                <a href="{{ route('schedules.show', $sch->uuid) }}" class="btn btn-xs btn-outline-secondary py-0 px-2 spa-link">
                                                    Detail <i class="bi bi-chevron-right small"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="timeline-body mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i> Durasi: {{ $sch->movie->duration }} menit
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
