@extends('_layouts.app')
@section('title', 'Ganti Password')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <form action="{{ route('password.update') }}" method="POST" data-ajax="true">
                @csrf
                @method('PUT')

                <div class="card custom-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Isi password lama anda">
                                <button class="btn btn-light" type="button" onclick="togglePassword('current_password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Isi password baru anda">
                                <button class="btn btn-light" type="button" onclick="togglePassword('new_password', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" placeholder="Konfirmasi password baru">
                                <button class="btn btn-light" type="button" onclick="togglePassword('new_password_confirmation', this)">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        const $newPassword = $("#new_password");
        const $newPasswordConfirmation = $("#new_password_confirmation");
        $newPasswordConfirmation.prop("disabled", !$newPassword.val());
        $newPassword.on("input", function() {
            if ($(this).val().length === 0) {
                $newPasswordConfirmation.prop("disabled", true).val("");
            } else {
                $newPasswordConfirmation.prop("disabled", false);
            }
        });
    </script>
@endsection
