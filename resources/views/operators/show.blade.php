@extends('_layouts.app')
@section('title', $operator->name)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informasi Akun</h4>
                    <small class="text-muted w-100">
                        Terakhir diperbarui {{ formatDate($operator->updated_at) }}
                    </small>
                </div>
                <div class="card-body pb-0">
                    <h3 class="mb-0 fw-bold mark d-inline">{{ $operator->name }}</h3>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Username:</strong>
                            <span class="float-end font-monospace">{{ $operator->username }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Role:</strong>
                            <span class="float-end fst-italic">{{ $operator->role->label }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Status Akun:</strong>
                            <span class="float-end">
                                <span class="badge text-bg-{{ $operator->status_color }}">
                                    {{ $operator->status_label }}
                                </span>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="{{ route('operators.edit', $operator->username) }}" class="btn btn-info spa-link">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button class="btn btn-danger" data-ajax="delete" data-url="{{ route('operators.destroy', $operator->username) }}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Aktivitas Terbaru</div>
                </div>
                <div class="card-body">
                    @if ($activities->isEmpty())
                        <p class="text-muted text-center">Tidak ada aktivitas terbaru.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($activities as $activity)
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <strong>{{ $activity->description }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <span class="badge bg-secondary">{{ $activity->log_name }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
