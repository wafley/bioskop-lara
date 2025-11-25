@extends('_layouts.app')
@section('title', 'Edit Operator')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ route('operators.update', $operator->id) }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')

                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $operator->name }}">
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ $operator->username }}">
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

                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                {{ old('status', $operator->status) ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                {{ old('status', $operator->status) ? 'Aktif' : 'Tidak Aktif' }}
                            </label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('operators.show', $operator->username) }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
