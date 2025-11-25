@extends('_layouts.app')
@section('title', 'Profile')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Ringkasan</div>
                </div>
                <div class="card-body pb-0">
                    <div class="main-profile-overview">
                        <div class="main-img-user profile-user">
                            @php
                                $placeholder = asset('assets/images/placeholders/profile-placeholder.jpg');
                            @endphp
                            <img alt="" src="{{ $placeholder }}">
                        </div>
                        <div class="d-flex justify-content-between mg-b-20">
                            <div>
                                <h5 class="main-profile-name">{{ Auth::user()->name ?? 'Admin' }}</h5>
                                <p class="main-profile-name-text">{{ Auth::user()->role->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <form action="{{ route('profile.update') }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')

                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Informasi Pribadi</div>
                    </div>
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input class="form-control" type="text" id="name" name="name" value="{{ Auth::user()->name }}">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input class="form-control" type="text" id="username" name="username" value="{{ Auth::user()->username }}">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Isi jika ingin mengganti password">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                            <span class="form-text text-muted">Kosongkan jika tidak ingin mengubah kata sandi</span>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                    placeholder="Konfirmasi password baru">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password_confirmation', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                            <span class="form-text text-muted">Kosongkan jika tidak ingin mengubah kata sandi</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
