@extends('_layouts.app')
@section('title', 'Edit ' . $studio->name)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <form action="{{ route('studios.update', $studio->slug) }}" method="POST" data-ajax="true">
        @csrf
        @method('PUT')

        {{-- <div class="row">
            <div class="col">
                <div class="card custom-card">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $studio->name }}"
                                placeholder="Masukkan nama studio">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('studios.show', $studio->slug) }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Dasar</h4>
                    </div>
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Studio</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $studio->name }}"
                                        placeholder="Contoh: Studio 1 Premiere">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status Studio</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="1" {{ $studio->status ? 'selected' : '' }}>Open (Tersedia)</option>
                                        <option value="0" {{ !$studio->status ? 'selected' : '' }}>Closed (Tutup)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Baris (Read-only)</label>
                                    <input type="number" class="form-control bg-light" value="{{ $studio->rows }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Kolom (Read-only)</label>
                                    <input type="number" class="form-control bg-light" value="{{ $studio->cols }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Kapasitas Total</label>
                                    <input type="number" class="form-control bg-light" value="{{ $studio->capacity }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="ti ti-info-circle fs-4 me-2"></i>
                            <div>
                                Jumlah baris dan kolom tidak dapat diubah untuk menjaga integritas data kursi.
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('studios.show', $studio->slug) }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Layout Kursi yang Sudah Ada --}}
        <div class="row">
            <div class="col">
                <div class="card custom-card border">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h4 class="card-title">Preview Layout Kursi</h4>
                                <span class="fw-bold text-primary">Total {{ $studio->capacity }} Kursi</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-secondary fw-bold">
                                    Konfigurasi: {{ $studio->rows }} x {{ $studio->cols }}
                                </span>

                                @if (!$studio->seats->contains('type', 'vip'))
                                    <button type="button" id="btn-generate-vip" class="btn btn-warning">
                                        <i class="ti ti-armchair-2 me-1"></i> Buat Kursi VIP
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="seats-wrapper" class="card-body">
                        <x-seats :seats="$seats" />
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script data-partial="1">
        $(document).ready(function() {
            $("#btn-generate-vip").on("click", function() {
                const btn = $(this);

                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Sistem akan mengubah kursi di baris tengah menjadi VIP. Lanjutkan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Generate!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ajaxRequest({
                            url: "{{ route('studios.add-vip', $studio->slug) }}",
                            method: "POST",
                            onSuccess: function(res) {
                                window.location.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
