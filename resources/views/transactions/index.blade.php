@extends('_layouts.app')
@section('title', 'Data Transaksi')

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
                                <label for="payment-method" class="form-label">Metode Pembayaran</label>
                                <select id="payment-method" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>
                            <div>
                                <label for="filter-status" class="form-label">Filter Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="success">Sukses</option>
                                    <option value="pending">Menunggu Pembayaran</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col d-flex align-items-center gap-3">
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
                                <table id="transactions-table" class="table mb-0 table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Invoice Number</th>
                                            <th>Tanggal</th>

                                            @role('admin')
                                                <th>Kasir</th>
                                            @endrole

                                            <th>Harga Total</th>
                                            <th>Metode Pembayaran</th>
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
        window.transactionsTable = window.transactionsTable || null;

        function initTransactionsTable() {
            if ($.fn.DataTable.isDataTable("#transactions-table")) {
                $("#transactions-table").DataTable().destroy();
            }

            window.transactionsTable = $("#transactions-table").DataTable({
                processing: false,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('transactions.data') }}",
                    data: function(d) {
                        d.payment_method = $("#payment-method").val();
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
                        data: "invoice_number",
                        name: "invoice_number",
                        searchable: true
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        searchable: false
                    },

                    @role('admin')
                        {
                            data: "cashier",
                            name: "cashier",
                            orderable: false,
                            searchable: false
                        },
                    @endrole

                    {
                        data: "total_price",
                        name: "total_price",
                        searchable: false
                    },
                    {
                        data: "payment_method",
                        name: "payment_method",
                        orderable: false,
                        searchable: false
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

        initTransactionsTable();

        // Table loader animation
        window.transactionsTable.on('processing.dt', function(e, settings, processing) {
            if (processing) {
                $('#table-loader').show();
            } else {
                $('#table-loader').hide();
            }
        });

        // Refresh table
        $("#refresh-btn").on("click", function() {
            window.transactionsTable.ajax.reload();
        });

        // Payment Method
        $("#payment-method").on("change", function() {
            window.transactionsTable.ajax.reload();
        });

        // Filter status
        $("#filter-status").on("change", function() {
            window.transactionsTable.ajax.reload();
        });
    </script>
@endsection
