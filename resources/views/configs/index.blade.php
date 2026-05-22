@extends('_layouts.app')
@section('title', 'Settings')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <form action="{{ route('config.update') }}" method="POST" data-ajax="true">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        @foreach ($fields as $key => $field)
                            <div class="mb-3">
                                @php
                                    $value = old($key, $settings[$key] ?? '');
                                @endphp

                                <label for="{{ $key }}" class="form-label">{{ $field['label'] }}</label>
                                <input type="{{ $field['type'] }}" name="{{ $key }}" id="{{ $key }}" class="form-control" value="{{ $value }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script data-partial="1">
        // Input Number handler
        $("input[type='number']").on("input", function() {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
    </script>
@endsection
