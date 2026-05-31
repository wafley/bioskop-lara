@extends('_layouts.app')
@section('title', 'Profile')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('profile.update', $user->username) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')

                <div class="card custom-card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Informasi Akun</h4>
                        <small class="text-muted w-100">
                            Terakhir diperbarui {{ formatDate($user->updated_at) }}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center mb-3">
                            <div class="avatar avatar-xxl mb-3">
                                <img src="{{ asset('assets/images/placeholders/profile-placeholder.jpg') }}" alt="{{ $user->name }}" class="rounded-circle img-fluid">
                            </div>

                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <span class="">{{ ucfirst($user->role->label) }}</span>
                        </div>

                        <hr />

                        <div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
                            </div>

                            <div>
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" value="{{ $user->username }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary spa-link">Kembali</a>
                    </div>
                </div>
            </form>
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
