@extends('_layouts.app')
@section('title', $cashier->name)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Informasi Akun</h4>
                    <small class="text-muted w-100">
                        Terakhir diperbarui {{ formatDate($cashier->updated_at) }}
                    </small>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center mb-3">
                        <div class="avatar avatar-xxl mb-3">
                            <img src="{{ asset('assets/images/placeholders/profile-placeholder.jpg') }}" alt="{{ $cashier->name }}" class="rounded-circle img-fluid">
                        </div>

                        <h5 class="mb-1">{{ $cashier->name }}</h5>
                        <span class="">{{ ucfirst($cashier->role->label) }}</span>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('cashiers.edit', $cashier->username) }}" class="btn btn-info spa-link w-100">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <button class="btn btn-danger w-100" data-ajax="delete" data-url="{{ route('cashiers.destroy', $cashier->username) }}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>

                    <hr />

                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th width="25%" class="text-muted fw-bold">Username</th>
                                    <td class="font-monospace">: {{ $cashier->username }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-bold">Status</th>
                                    <td class="font-monospace">
                                        :
                                        <span class="badge text-bg-{{ $cashier->status_color }}">
                                            {{ $cashier->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-bold">Dibuat Pada</th>
                                    <td class="font-monospace">: {{ formatDate($cashier->created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
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
