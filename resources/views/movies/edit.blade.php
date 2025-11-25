@extends('_layouts.app')
@section('title', 'Edit Film')

@section('styles')
    <link rel="stylesheet" href="{{ asset('templates/libs/choices.js/public/assets/styles/choices.min.css') }}" data-partial="1">
@endsection

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <form action="{{ route('movies.update', $movie->slug) }}" method="POST" data-ajax="true">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">Poster</div>
                    </div>
                    <div class="card-body">
                        <img id="poster-preview" src="{{ $movie->poster }}" alt="Preview Poster" class="img-fluid rounded border">
                        <input type="file" name="poster" id="poster" accept="image/*" hidden>
                    </div>
                    <div class="card-footer">
                        <label for="poster" class="btn btn-primary d-block w-100">
                            <i class="bi bi-file-earmark-image"></i>
                            Pilih Poster
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ $movie->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="release_date" class="form-label">Tanggal Rilis</label>
                            <input type="text" name="release_date" id="release_date" class="form-control" value="{{ $movie->release_date }}">
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Durasi</label>
                            <input type="text" name="duration" id="duration" class="form-control"
                                value="{{ formatDuration($movie->getRawOriginal('duration'), 'input') }}">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="coming_soon" @selected($movie->status === 'coming_soon')>Segera Tayang</option>
                                <option value="now_showing" @selected($movie->status === 'now_showing')>Tersedia</option>
                                <option value="ended" @selected($movie->status === 'ended')>Selesai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="genre" class="form-label">Genre</label>
                            <select class="form-control" name="genre[]" id="genre" multiple>
                                @foreach (\App\Models\Movie::GENRES as $key => $label)
                                    <option value="{{ $key }}" @if (in_array(Str::title($key), $movie->genre)) selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="cast" class="form-label">Pemeran</label>
                            <input type="text" name="cast[]" id="cast" class="form-control" value="{{ implode(',', $movie->cast) }}">
                        </div>

                        <div class="mb-3">
                            <label for="director" class="form-label">Sutradara</label>
                            <input type="text" name="director" id="director" class="form-control" value="{{ $movie->director }}">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description" rows="3">{{ $movie->description }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('movies.show', $movie->slug) }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/choices.js/public/assets/scripts/choices.min.js') }}" data-partial="1"></script>
    <script src="{{ asset('templates/libs/cleave.js/cleave.min.js') }}" data-partial="1"></script>

    {{-- Preview poster --}}
    <script data-partial="1">
        $('#poster').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#poster-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                $('#poster-preview').attr('src', '{{ $movie->poster }}');
            }
        });
    </script>

    {{-- Format release date input --}}
    <script data-partial="1">
        new Cleave("#release_date", {
            date: true,
            delimiter: "-",
            datePattern: ["d", "m", "Y"],
        });
    </script>

    {{-- Format duration input --}}
    <script data-partial="1">
        new Cleave("#duration", {
            time: true,
            timePattern: ['h', 'm'],
        });
    </script>

    <script data-partial="1">
        $(function() {
            // Initialize genre select
            const genreSelect = document.getElementById('genre');
            if (genreSelect) {
                new Choices(genreSelect, {
                    removeItemButton: true,
                    searchEnabled: true,
                    position: "bottom",
                });
            }

            // Initialize cast input
            const castInput = document.getElementById('cast');
            if (castInput) {
                new Choices(castInput, {
                    removeItemButton: true,
                    delimiter: ',',
                    editItems: true,
                    duplicateItemsAllowed: false,
                });
            }
        });
    </script>
@endsection
