@extends('_layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <h5 class="fw-bold">Selamat datang, Admin!</h5>
                    <p class="text-muted mb-0">Ini adalah halaman dashboard khusus admin.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
