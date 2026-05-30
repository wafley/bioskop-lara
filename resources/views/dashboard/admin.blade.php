@extends('_layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12 col-md-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card custom-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Pendapatan</h6>
                        <h4 class="mb-0 fw-bold">{{ formatPrice($totalRevenue) }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded p-3">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card custom-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Transaksi</h6>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalTransactions) }}</h4>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3 mb-3 mb-md-0">
            <div class="card custom-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Kasir Aktif</h6>
                        <h4 class="mb-0 fw-bold">{{ number_format($activeCashiers) }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded p-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card custom-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Film Sedang Tayang</h6>
                        <h4 class="mb-0 fw-bold">{{ number_format($activeMovies) }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="fas fa-film fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Jam Sibuk Transaksi</h5>
                    <small class="text-muted w-100">Statistik transaksi berdasarkan jam</small>
                </div>
                <div class="card-body">
                    <div id="peakHoursChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 col-lg-8 mb-4 mb-lg-0">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Film Terpopuler</h5>
                    <small class="text-muted w-100">Statistik film berdasarkan total tiket yang terjual</small>
                </div>
                <div class="card-body">
                    <div id="popularMoviesChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Statistik Studio</h5>
                    <small class="text-muted w-100">Distribusi tiket terjual per studio</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="studioStatsChart" style="min-height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Kasir Teraktif</h5>
                    <a href="{{ route('cashiers.index') }}" class="btn btn-sm btn-outline-primary spa-link">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Kasir</th>
                                    <th class="text-center">Total Transaksi</th>
                                    <th class="text-end">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCashiers as $index => $cashier)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="fw-semibold">{{ $cashier->name }}</td>
                                        <td class="text-center">{{ number_format($cashier->total_transactions) }}</td>
                                        <td class="text-end fw-bold text-success">{{ formatPrice($cashier->total_revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data transaksi kasir.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Transaksi Terbaru</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary spa-link">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Kasir</th>
                                    <th>Film</th>
                                    <th class="text-center">Tiket</th>
                                    <th class="text-end">Total Pembayaran</th>
                                    <th class="text-end">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td class="fw-semibold">
                                            <a href="{{ route('transactions.show', $transaction->invoice_number) }}"
                                                class="text-primary spa-link">#{{ $transaction->invoice_number }}</a>
                                        </td>
                                        <td>{{ $transaction->cashier->name ?? '-' }}</td>
                                        <td>
                                            @if ($transaction->schedule && $transaction->schedule->movie)
                                                {{ Str::limit($transaction->schedule->movie->title, 20) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $transaction->total_tickets }}</td>
                                        <td class="text-end fw-semibold">{{ formatPrice($transaction->total_price) }}</td>
                                        <td class="text-end text-muted small">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/apexcharts/apexcharts.min.js') }}" data-partial="1"></script>
    <script data-partial="1">
        (function() {
            var popularMovies = @json($popularMovies);
            var studioStats = @json($studioStats);
            var peakHours = @json($peakHours);

            function renderCharts() {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(renderCharts, 100);
                    return;
                }

                // Area Chart - Peak Hours
                var elPeakHours = document.querySelector("#peakHoursChart");
                if (elPeakHours && peakHours.length > 0) {
                    elPeakHours.innerHTML = '';
                    var peakHoursCategories = peakHours.map(function(h) {
                        return h.hour;
                    });
                    var peakHoursData = peakHours.map(function(h) {
                        return parseInt(h.total_transactions);
                    });

                    var areaOptions = {
                        series: [{
                            name: 'Total Transaksi',
                            data: peakHoursData
                        }],
                        chart: {
                            type: 'area',
                            height: 300,
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#0d6efd'],
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2
                        },
                        xaxis: {
                            categories: peakHoursCategories,
                            labels: {
                                style: {
                                    cssClass: 'text-muted fw-normal'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: function(val) {
                                    return val.toFixed(0);
                                }
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + " transaksi";
                                }
                            }
                        }
                    };
                    var areaChart = new ApexCharts(elPeakHours, areaOptions);
                    areaChart.render();
                }

                // Bar Chart - Popular Movies
                var elMovies = document.querySelector("#popularMoviesChart");
                if (elMovies && popularMovies.length > 0) {
                    elMovies.innerHTML = '';
                    var movieNames = popularMovies.map(function(m) {
                        return m.title;
                    });
                    var movieTickets = popularMovies.map(function(m) {
                        return parseInt(m.total_tickets);
                    });

                    var barOptions = {
                        series: [{
                            name: 'Tiket Terjual',
                            data: movieTickets
                        }],
                        chart: {
                            type: 'bar',
                            height: 300,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                                distributed: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: movieNames,
                        },
                        legend: {
                            show: false
                        }
                    };
                    var barChart = new ApexCharts(elMovies, barOptions);
                    barChart.render();
                }

                // Pie Chart - Studio Stats
                var elStudios = document.querySelector("#studioStatsChart");
                if (elStudios && studioStats.length > 0) {
                    elStudios.innerHTML = '';
                    var studioNames = studioStats.map(function(s) {
                        return s.name;
                    });
                    var studioTickets = studioStats.map(function(s) {
                        return parseInt(s.total_tickets);
                    });

                    var pieOptions = {
                        series: studioTickets,
                        chart: {
                            type: 'pie',
                            height: 300
                        },
                        labels: studioNames,
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };
                    var pieChart = new ApexCharts(elStudios, pieOptions);
                    pieChart.render();
                }
            }

            renderCharts();
        })();
    </script>
@endsection
