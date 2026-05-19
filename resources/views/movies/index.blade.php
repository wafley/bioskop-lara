@extends('_layouts.app')
@section('title', 'Movies')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <label class="form-label">Filter Status</label>
                            <select class="form-select w-auto" onchange="filterStatus(this.value)">
                                <option value="">Semua Status</option>
                                <option value="now_showing" {{ request('status') === 'now_showing' ? 'selected' : '' }}>Sedang Tayang</option>
                                <option value="coming_soon" {{ request('status') === 'coming_soon' ? 'selected' : '' }}>Segera Tayang</option>
                                <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Berakhir</option>
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <form method="GET" action="{{ route('movies.index') }}">
                                <label class="form-label">Cari Film</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul film..." value="{{ request('search') }}">

                                    @if (request('status') !== null)
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                    @endif

                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            @role('admin')
                                <a href="{{ route('movies.create') }}" class="btn btn-primary spa-link">
                                    <i class="me-2 bi bi-plus"></i>
                                    Tambah
                                </a>
                            @endrole

                            <a href="{{ route('movies.index') }}" class="btn btn-success spa-link">
                                <i class="me-2 ti ti-rotate"></i>
                                Refresh
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        @forelse ($movies as $movie)
                            <div class="col-md-6 col-lg-4 col-xl-2">
                                <div class="card custom-card shadow-sm border w-100">
                                    <div class="position-relative overflow-hidden">
                                        <img src="{{ $movie->poster }}" class="card-img-top w-100" alt="Poster {{ $movie->title }}">

                                        <span class="badge position-absolute top-0 start-0 m-2 mt-2 text-bg-{{ $movie->status_color }} shadow-sm">
                                            {{ $movie->status_label }}
                                        </span>

                                    </div>

                                    <div class="card-body">
                                        <h5 class="card-title fw-bold text-primary text-truncate mb-0">
                                            {{ $movie->title }}
                                        </h5>

                                        <small class="text-muted">
                                            <i class="bi bi-tags me-1"></i>
                                            {{ implode(', ', $movie->genre) }}
                                        </small>

                                        <a href="{{ route('movies.show', $movie->slug) }}" class="btn btn-outline-light w-100 mt-2 rounded-pill">
                                            <i class="bi bi-eye"></i>
                                            <span>Lihat Detail</span>
                                        </a>
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

                    {{ $movies->links() }}
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
