@extends('_layouts.app')
@section('title', $studio->name)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('styles')
    <style data-partial="1">
        .custom-card.bg-warning-gradient {
            box-shadow: 0 0 12px rgba(var(--warning-rgb), 0.6) !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h4 class="card-title">Informasi Studio</h4>
                            <small class="text-muted w-100">
                                Terakhir diperbarui {{ formatDate($studio->updated_at) }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('studios.edit', $studio->slug) }}" class="btn btn-info spa-link">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <button class="btn btn-danger" data-ajax="delete" data-url="{{ route('studios.destroy', $studio->slug) }}">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h1 class="fs-1 fw-bold mark d-inline">{{ $studio->name }}</h1>
                        <span class='badge text-bg-{{ $studio->status_color }}'>{{ $studio->status_label }}</span>
                    </div>

                    <div class="d-flex flex-wrap flex-lg-nowrap align-items-center gap-3 mb-0">
                        <div class="card custom-card mb-0 bg-primary-gradient">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded p-2">
                                        <i class="ti ti-armchair fs-1 text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-6 mb-0 text-white">Regular</h6>
                                        <h3 class="fs-3 text-white">{{ $seat_types->regular }} Kursi</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card custom-card mb-0 bg-warning-gradient">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded p-2">
                                        <i class="ti ti-armchair-2 fs-1 text-warning"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-6 mb-0 text-white">VIP</h6>
                                        <h3 class="fs-3 text-warning text-white">{{ $seat_types->vip }} Kursi</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card custom-card mb-0 bg-secondary-gradient">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded p-2">
                                        <i class="ti ti-armchair-off fs-1 text-secondary"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-6 mb-0 text-white">Disabled</h6>
                                        <h3 class="fs-3 text-white">{{ $seat_types->disabled }} Kursi</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="card custom-card border">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    <h4 class="card-title">Layout Kursi</h4>
                                    <span class="fw-bold text-primary w-100">Total {{ $studio->capacity }} Kursi</span>
                                </div>
                                <span class="badge bg-secondary fw-bold">
                                    Konfigurasi: {{ $studio->rows }} x {{ $studio->cols }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <x-seats :seats="$seats" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
