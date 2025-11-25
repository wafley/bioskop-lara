@extends('_layouts.auth')
@section('title', 'Masuk')

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <form action="{{ route('login.submit') }}" method="POST" data-ajax="true">
                @csrf
                @method('POST')

                <div class="card custom-card">
                    <div class="card-header">
                        <div class="d-flex flex-column align-items-center justify-content-center w-100">
                            <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" width="80" alt="logo">
                            <h2 class="fw-bold text-primary">Selamat Datang!</h2>
                            <p>Silahkan masuk untuk menggunakan aplikasi</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username anda"
                                value="{{ old('username') }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi kamu"
                                    value="{{ old('password') }}">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input primary" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-dark" for="remember">
                                Ingat saya
                            </label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary d-block w-100">
                            Lanjut
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
