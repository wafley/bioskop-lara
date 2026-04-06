@extends('_layouts.app')
@section('title', 'Rincian Jadwal')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <div class="bg-light rounded text-center p-3">
                            @if ($schedule->movie->poster)
                                <img src="{{ $schedule->movie->poster }}" class="rounded shadow border" alt="Poster" width="180">
                            @endif
                        </div>

                        <div class="flex-grow-1 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-{{ $schedule->status_label->class }} mb-1">
                                        <i class="{{ $schedule->status_label->icon }} me-1"></i>
                                        {{ $schedule->status_label->text }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <h2 class="fw-bold d-inline-block">
                                            {{ $schedule->movie->title }}
                                        </h2>
                                        <a href="{{ route('movies.show', $schedule->movie->slug) }}" class="text-primary">
                                            Detail <i class='bi bi-arrow-right'></i>
                                        </a>
                                    </div>
                                    <p class="text-muted">
                                        <i class="bi bi-tags me-1"></i>
                                        {{ implode(', ', $schedule->movie->genre) }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">Harga Tiket</small>
                                    <h4 class="fw-bold text-primary">
                                        {{ formatPrice($schedule->price) }}
                                    </h4>
                                </div>
                            </div>

                            <div class="row bg-light rounded shadow-sm g-3">
                                <div class="col-6 col-md-3 my-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Tanggal</div>
                                    <div class="fw-bold">{{ formatDate($schedule->show_date, false) }}</div>
                                </div>
                                <div class="col-6 col-md-3 my-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Mulai</div>
                                    <div class="fw-bold text-success">{{ substr($schedule->start_time, 0, 5) }}</div>
                                </div>
                                <div class="col-6 col-md-3 my-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Selesai</div>
                                    <div class="fw-bold text-danger">{{ substr($schedule->end_time, 0, 5) }}</div>
                                </div>
                                <div class="col-6 col-md-3 my-3 text-center">
                                    <div class="text-muted small">Durasi</div>
                                    <div class="fw-bold">{{ $schedule->movie->duration }}</div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($schedule->status_label->text == 'Selesai')
                                        <button class="btn btn-info w-100" disabled>
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    @else
                                        <a href="{{ route('schedules.edit', $schedule->uuid) }}" class="btn btn-info w-100 spa-link">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                    @endif
                                    <button class="btn btn-danger w-100" data-ajax="delete" data-url="{{ route('schedules.destroy', $schedule->uuid) }}">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                                <hr>
                                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary w-100 spa-link">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Studio</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-door-open fs-1 text-primary mb-2"></i>
                        <h4 class="fw-bold">{{ $schedule->studio->name }}</h4>
                        <p class="text-muted mb-0">Kapasitas: {{ $schedule->studio->capacity ?? 'N/A' }} Kursi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
