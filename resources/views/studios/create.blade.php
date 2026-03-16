@extends('_layouts.app')
@section('title', 'Tambah Studio')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <form action="{{ route('studios.store') }}" method="POST" data-ajax="true">
        @csrf
        @method('POST')

        <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama studio">
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="rows" class="form-label">Baris</label>
                                    <input type="number" name="rows" id="rows" class="form-control" value="{{ $rows ?? 0 }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="cols" class="form-label">Kolom</label>
                                    <input type="number" name="cols" id="cols" class="form-control" value="{{ $cols ?? 0 }}">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Kapasitas</label>
                                    <input type="number" name="capacity" id="capacity" class="form-control"
                                        value="{{ ($rows ?? 0) * ($cols ?? 0) }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="generate_vip" id="generate-vip">
                            <label class="form-check-label" for="generate-vip">Generate VIP Otomatis</label>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('studios.index') }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card custom-card border">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h4 class="card-title">Layout Kursi</h4>
                                <span id="seats-label" class="fw-bold text-primary w-100">
                                    Total {{ ($rows ?? 0) * ($cols ?? 0) }} Kursi
                                </span>
                            </div>
                            <span class="badge bg-secondary fw-bold">
                                Konfigurasi: {{ $rows ?? 0 }} x {{ $cols ?? 0 }}
                            </span>
                        </div>
                    </div>
                    <div id="seats-preview" class="card-body pb-0">
                        <x-seats :rows="$rows ?? 0" :cols="$cols ?? 0" :generate-vip="$generateVip ?? false" />
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script data-partial="1">
        function renderSeats() {
            let rows = $("#rows").val();
            let cols = $("#cols").val();
            let generateVip = $("#generate-vip").is(':checked');

            if (!rows || !cols || rows <= 0 || cols <= 0) {
                $("#seats-preview").html(`
                    <p class="text-muted text-center">Masukkan jumlah baris & kolom untuk menampilkan preview kursi.</p>
                `);

                $("#seats-label").text("Total 0 Kursi");
                $("#layout-label").text("0 x 0");
                return;
            }

            ajaxRequest({
                url: "{{ route('studios.render') }}",
                method: "POST",
                data: {
                    rows: rows,
                    cols: cols,
                    generate_vip: generateVip
                },
                onSuccess: function(res) {
                    $("#seats-preview").html(res.html);
                    $("#seats-label").text("Total " + res.total + " Kursi");
                    $("#layout-label").text(res.size);
                    $("#capacity").val(res.total);
                }
            });
        }

        $("#rows, #cols").on("keyup change", renderSeats);
        $("#generate-vip").on("change", renderSeats);

        // initial render
        renderSeats();
    </script>
@endsection
