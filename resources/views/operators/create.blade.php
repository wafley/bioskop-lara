@extends('_layouts.app')
@section('title', 'Tambah Operator')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <form action="{{ route('operators.store') }}" method="POST" data-ajax="true">
        @csrf
        @method('POST')

        <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama operator">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control"
                                placeholder="Masukkan username untuk operator">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan password untuk operator">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                    placeholder="Konfirmasi password">
                                <button class="btn btn-light" type="button" onclick="togglePassword('password_confirmation', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('operators.index') }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
