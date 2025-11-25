@extends('_layouts.app')
@section('title', 'Studios')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            <div>
                                <label for="filter-status" class="form-label">Filter Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="1">Open</option>
                                    <option value="0">Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            <a href="{{ route('studios.create') }}" class="btn btn-primary spa-link">
                                <i class="me-2 ti ti-user-plus"></i>
                                Tambah
                            </a>
                            <button type="button" id="refresh-btn" class="btn btn-success">
                                <i class="me-2 ti ti-rotate"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="table-responsive">
                                <div id="table-loader" class="text-center" style="display:none;">
                                    <div class="spinner-border text-primary d-block mx-auto" aria-hidden="true"></div>
                                    <span role="status">Sedang memproses...</span>
                                </div>
                                <table id="studios-table" class="table mb-0 table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Studio</th>
                                            <th>Kapasitas</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/datatables/datatables.min.js') }}" data-partial="1"></script>
    <script data-partial="1">
        window.studiosTable = window.studiosTable || null;

        function initStudiosTable() {
            if ($.fn.DataTable.isDataTable("#studios-table")) {
                $("#studios-table").DataTable().destroy();
            }

            window.studiosTable = $("#studios-table").DataTable({
                processing: false,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('studios.data') }}",
                    data: function(d) {
                        d.status = $("#filter-status").val();
                    },
                    error: function(xhr) {
                        if (xhr.status === 401 || xhr.status === 419) {
                            showToast("error", "Sesi login habis, silakan login ulang!");
                            window.location.href = window.routes.login;
                        }
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "capacity",
                        name: "capacity"
                    },
                    {
                        data: "status",
                        name: "status",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                        width: 180
                    },
                ],
                order: [
                    [0, 'desc']
                ],
                pageLength: 5,
                lengthMenu: [
                    [5, 15, 30, 50, 75, 100],
                    [5, 15, 30, 50, 75, 100]
                ],
                language: {
                    url: "{{ asset('templates/js/i18n/id.json') }}"
                }
            });
        }

        initStudiosTable();

        // Table loader animation
        window.studiosTable.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                $('#table-loader').show();
            } else {
                $('#table-loader').hide();
            }
        });

        // Refresh table
        $("#refresh-btn").on("click", function() {
            console.log('Clicked');
            window.studiosTable.ajax.reload();
        });

        // Filter status
        $("#filter-status").on("change", function() {
            window.studiosTable.ajax.reload();
        });
    </script>
@endsection
