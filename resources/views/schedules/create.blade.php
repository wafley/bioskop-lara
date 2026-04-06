@extends('_layouts.app')
@section('title', 'Tambah Jadwal')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('templates/libs/choices.js/public/assets/styles/choices.min.css') }}" data-partial="1">
@endsection

@section('content')
    <form action="{{ route('schedules.store') }}" method="POST" data-ajax="true">
        @csrf
        @method('POST')

        <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="show_date" class="form-label">Tanggal</label>
                            <input type="text" name="show_date" id="show_date" class="form-control" placeholder="DD-MM-YYYY">
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="movie" class="form-label">Movie</label>
                                    <select class="form-control" name="movie_id" id="movie" placeholder="This is a search placeholder" data-trigger>
                                        <option value="">Pilih Movie</option>
                                        @foreach ($movies as $movie)
                                            <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="studio" class="form-label">Studio</label>
                                    <select class="form-control" name="studio_id" id="studio" placeholder="This is a search placeholder" data-trigger>
                                        <option value="">Pilih Studio</option>
                                        @foreach ($studios as $studio)
                                            <option value="{{ $studio->id }}">{{ $studio->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label">Jam Mulai</label>
                            <input type="text" name="start_time" id="start_time" class="form-control" placeholder="hh:mm">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga Tiket</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    Rp.
                                </span>
                                <input type="text" class="form-control" id="price" name="price" readonly>
                            </div>
                            <span class="form-text text-muted">Harga tiket akan otomatis diisi sesuai dengan hari penayangan.</span>
                        </div>
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
    <script src="{{ asset('templates/libs/choices.js/public/assets/scripts/choices.min.js') }}" data-partial="1"></script>
    <script src="{{ asset('templates/libs/cleave.js/cleave.min.js') }}" data-partial="1"></script>

    {{-- Format show date input --}}
    <script data-partial="1">
        new Cleave("#show_date", {
            date: true,
            delimiter: "-",
            datePattern: ["d", "m", "Y"],
        });
    </script>

    {{-- Format start time input --}}
    <script data-partial="1">
        new Cleave("#start_time", {
            time: true,
            timePattern: ['h', 'm'],
        });
    </script>

    <script data-partial="1">
        $(function() {
            // Initialize movie select
            const movieSelect = document.getElementById('movie');
            if (movieSelect) {
                new Choices(movieSelect, {
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Pilih Movie',
                    position: "bottom",
                });
            }

            // Initialize studio select
            const studioSelect = document.getElementById('studio');
            if (studioSelect) {
                new Choices(studioSelect, {
                    removeItemButton: true,
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Pilih Studio',
                    position: "bottom",
                });
            }
        });
    </script>

    <script data-partial="1">
        const movieDurations = @json($movies->mapWithKeys(fn($m) => [$m->id => (int) $m->duration]));
    </script>

    <script data-partial="1">
        const priceInput = document.getElementById('price');
        const showDateInput = document.getElementById('show_date');

        function setAutoPrice() {
            const dateVal = showDateInput.value;
            if (dateVal.length === 10) {
                const parts = dateVal.split("-");
                const date = new Date(parts[2], parts[1] - 1, parts[0]);
                const day = date.getDay();

                let price = 40000;
                if (day === 6) price = 50000;
                if (day === 0 || day === 6) price = 65000;

                priceInput.value = price;
            }
        }

        $(showDateInput).on('input', setAutoPrice);
    </script>
@endsection
