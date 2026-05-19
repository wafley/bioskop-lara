@extends('_layouts.app')
@section('title', $schedule->movie->title . ' (' . substr($schedule->start_time, 0, 5) . ' - ' . substr($schedule->end_time, 0, 5) . ')')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        @if ($schedule->movie->poster)
                            <img src="{{ $schedule->movie->poster }}" class="rounded shadow-sm" alt="Poster" width="160">
                        @endif

                        <div class="flex-grow-1 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
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

                            <div class="d-flex flex-wrap bg-light rounded shadow-sm py-3 g-3">
                                <div class="col-6 col-md-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Tanggal</div>
                                    <h5 class="fw-bold">{{ formatDate($schedule->show_date, false) }}</h5>
                                </div>
                                <div class="col-6 col-md-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Mulai</div>
                                    <h5 class="fw-bold text-success">{{ substr($schedule->start_time, 0, 5) }}</h5>
                                </div>
                                <div class="col-6 col-md-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Selesai</div>
                                    <h5 class="fw-bold text-danger">{{ substr($schedule->end_time, 0, 5) }}</h5>
                                </div>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="text-muted small">Durasi</div>
                                    <h5 class="fw-bold">{{ $schedule->movie->duration }}</h5>
                                </div>
                            </div>

                            <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary w-100 mt-auto spa-link">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Studio</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-door-open fs-1 text-primary mb-2"></i>
                        <h4 class="fw-bold mb-0">{{ $schedule->studio->name }}</h4>
                        <p class="text-muted mb-2">Kapasitas: {{ $schedule->studio->capacity ?? 'N/A' }} Kursi</p>
                        <a href="{{ route('studios.show', $schedule->studio->slug) }}" class="btn btn-sm btn-outline-secondary">
                            Detail <i class='bi bi-arrow-right'></i>
                        </a>
                    </div>
                    <x-seats :seats="$seats" :bookedSeatIds="$bookedSeatIds" />
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Pemesanan</h4>
                </div>
                <div class="card-body">
                    <!-- Informasi pemesanan akan ditampilkan di sini -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        // 
    </script>
@endsection
