@extends('_layouts.app')
@section('title', 'Studios')

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
                            <label class="form-label">Filter Status</label>
                            <select class="form-select w-auto" onchange="filterStatus(this.value)">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Open</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            <a href="{{ route('studios.create') }}" class="btn btn-primary spa-link">
                                <i class="me-2 bi bi-plus"></i>
                                Tambah
                            </a>
                            <a href="{{ route('studios.index') }}" class="btn btn-success spa-link">
                                <i class="me-2 ti ti-rotate"></i>
                                Refresh
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        @forelse ($studios as $studio)
                            <div class="col-md-6 col-lg-4">
                                <div class="card custom-card shadow-sm border">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">{{ $studio->name }}</h5>
                                            <small class="text-muted">
                                                Kapasitas: {{ $studio->capacity }}
                                            </small>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge text-bg-{{ $studio->status_color }}">
                                                {{ $studio->status_label }}
                                            </span>

                                            <a href="{{ route('studios.show', $studio->slug) }}" class="btn btn-sm btn-outline-secondary spa-link">
                                                Detail <i class='bi bi-arrow-right'></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col">
                                <div class="text-center text-muted py-5">
                                    Data studio tidak tersedia
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        function filterStatus(status) {
            let url = new URL(window.location.href);

            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }

            window.location.href = url.toString();
        }
    </script>
@endsection
