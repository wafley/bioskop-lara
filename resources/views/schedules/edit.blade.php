@extends('_layouts.app')
@section('title', 'Ubah Jadwal')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('templates/libs/choices.js/public/assets/styles/choices.min.css') }}" data-partial="1">
@endsection

@section('content')
    <form action="{{ route('schedules.update', $schedule->uuid) }}" method="POST" data-ajax="true">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="show_date" class="form-label">Tanggal</label>
                            <input type="text" name="show_date" id="show_date" class="form-control" value="{{ $schedule->show_date }}" placeholder="DD-MM-YYYY">
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="movie" class="form-label">Movie</label>
                                    <select class="form-control" name="movie_id" id="movie" placeholder="This is a search placeholder" data-trigger>
                                        <option value="">Pilih Movie</option>
                                        @foreach ($movies as $movie)
                                            <option value="{{ $movie->id }}" {{ $schedule->movie_id == $movie->id ? 'selected' : '' }}>
                                                {{ $movie->title }}
                                            </option>
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
                                            <option value="{{ $studio->id }}" {{ $schedule->studio_id == $studio->id ? 'selected' : '' }}>
                                                {{ $studio->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label">Jam Mulai</label>
                            <input type="text" name="start_time" id="start_time" class="form-control" value="{{ substr($schedule->start_time, 0, 5) }}" placeholder="hh:mm">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga Tiket</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    Rp.
                                </span>
                                <input type="text" class="form-control currency-input" id="price" name="price" value="{{ (int) $schedule->price }}" readonly>
                            </div>
                            <span class="form-text text-muted">Harga tiket akan otomatis diisi sesuai dengan hari penayangan.</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
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
            // Initialize choices using jQuery
            const $movieSelect = $('#movie');
            if ($movieSelect.length) {
                new Choices($movieSelect[0], {
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Pilih Movie',
                    position: "bottom",
                });
            }

            const $studioSelect = $('#studio');
            if ($studioSelect.length) {
                new Choices($studioSelect[0], {
                    removeItemButton: true,
                    searchEnabled: true,
                    placeholder: true,
                    placeholderValue: 'Pilih Studio',
                    position: "bottom",
                });
            }

            // Price auto calculation logic
            const $priceInput = $('#price');
            const $showDateInput = $('#show_date');

            const setAutoPrice = () => {
                const dateVal = $showDateInput.val();
                
                if (dateVal?.length === 10) {
                    const [dayStr, monthStr, yearStr] = dateVal.split("-");
                    const date = new Date(yearStr, monthStr - 1, dayStr);
                    const day = date.getDay();

                    let price = 40000;
                    if (day === 5) price = 50000;
                    if ([0, 6].includes(day)) price = 65000;

                    $priceInput.val(price).trigger('input');
                }
            };

            $showDateInput.on('input', setAutoPrice);
        });
    </script>
@endsection
