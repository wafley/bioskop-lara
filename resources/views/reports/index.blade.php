@extends('_layouts.app')
@section('title', 'Laporan & Statistik')

@section('breadcrumb')
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="spa-link">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan & Statistik</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <form action="{{ route('reports.index') }}" method="GET" class="spa-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $start_date }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $end_date }}">
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-filter me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Pendapatan</h6>
                        <h3 class="mb-0 fw-bold">{{ formatPrice($total_revenue) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded p-3">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Tiket Terjual</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($total_tickets) }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                        <i class="fas fa-ticket-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Transaksi</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($total_transactions) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="fw-bold">Grafik Pendapatan Harian</h5>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/apexcharts/apexcharts.min.js') }}" data-partial="1"></script>
    <script data-partial="1">
        (function() {
            var dailyData = @json($daily_data);
            var dates = dailyData.map(function(item) {
                return item.date;
            });
            var revenues = dailyData.map(function(item) {
                return parseFloat(item.revenue);
            });

            var options = {
                series: [{
                    name: 'Pendapatan',
                    data: revenues
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight',
                    width: 3
                },
                colors: ['#0d6efd'],
                markers: {
                    size: 4,
                    colors: ['#fff'],
                    strokeColors: '#0d6efd',
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    type: 'datetime',
                    categories: dates
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy'
                    },
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                },
            };

            function renderChart() {
                if (typeof ApexCharts !== 'undefined') {
                    var el = document.querySelector("#revenueChart");
                    if (el) {
                        el.innerHTML = '';
                        var chart = new ApexCharts(el, options);
                        chart.render();
                    }
                } else {
                    setTimeout(renderChart, 100);
                }
            }

            renderChart();
        })();
    </script>
@endsection
