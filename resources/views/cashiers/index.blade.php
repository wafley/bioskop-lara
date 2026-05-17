@extends('_layouts.app')
@section('title', 'Data Kasir')

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
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
                            <a href="{{ route('cashiers.create') }}" class="btn btn-primary spa-link">
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
                            <div class="table-responsive position-relative">
                                <div id="table-loader" class="text-center" style="display:none;">
                                    <div class="spinner-border text-primary d-block mx-auto" aria-hidden="true"></div>
                                    <span role="status">Sedang memproses...</span>
                                </div>
                                <table id="cashiers-table" class="table mb-0 table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Tanggal dibuat</th>
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
        window.cashiersTable = window.cashiersTable || null;

        function initCashiersTable() {
            if ($.fn.DataTable.isDataTable("#cashiers-table")) {
                $("#cashiers-table").DataTable().destroy();
            }

            window.cashiersTable = $("#cashiers-table").DataTable({
                processing: false,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('cashiers.data') }}",
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
                        data: "username",
                        name: "username"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
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
                    [0, "desc"]
                ],
                pageLength: 15,
                lengthMenu: [
                    [15, 30, 50, 75, 100],
                    [15, 30, 50, 75, 100]
                ],
                language: {
                    url: "{{ asset('templates/js/i18n/id.json') }}"
                }
            });
        }

        initCashiersTable();

        // Table loader animation
        window.cashiersTable.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                $('#table-loader').show();
            } else {
                $('#table-loader').hide();
            }
        });

        // Refresh table
        $("#refresh-btn").on("click", function() {
            window.cashiersTable.ajax.reload();
        });

        // Filter status
        $("#filter-status").on("change", function() {
            window.cashiersTable.ajax.reload();
        });
    </script>
@endsection
