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
        $(function() {
            const popularMovies = @json($popularMovies);
            const studioStats = @json($studioStats);
            const peakHours = @json($peakHours);

            const renderCharts = () => {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(renderCharts, 100);
                    return;
                }

                // Area Chart - Peak Hours
                const $elPeakHours = $("#peakHoursChart");
                if ($elPeakHours.length && peakHours.length > 0) {
                    $elPeakHours.empty();
                    const peakHoursCategories = peakHours.map(h => h.hour);
                    const peakHoursData = peakHours.map(h => parseInt(h.total_transactions));

                    const areaOptions = {
                        series: [{
                            name: 'Total Transaksi',
                            data: peakHoursData
                        }],
                        chart: {
                            type: 'area',
                            height: 300,
                            toolbar: { show: false }
                        },
                        colors: ['#0d6efd'],
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: {
                            categories: peakHoursCategories,
                            labels: { style: { cssClass: 'text-muted fw-normal' } }
                        },
                        yaxis: {
                            labels: { formatter: val => val.toFixed(0) }
                        },
                        tooltip: {
                            y: { formatter: val => `${val} transaksi` }
                        }
                    };
                    const areaChart = new ApexCharts($elPeakHours[0], areaOptions);
                    areaChart.render();
                }

                // Bar Chart - Popular Movies
                const $elMovies = $("#popularMoviesChart");
                if ($elMovies.length && popularMovies.length > 0) {
                    $elMovies.empty();
                    const movieNames = popularMovies.map(m => m.title);
                    const movieTickets = popularMovies.map(m => parseInt(m.total_tickets));

                    const barOptions = {
                        series: [{
                            name: 'Tiket Terjual',
                            data: movieTickets
                        }],
                        chart: {
                            type: 'bar',
                            height: 300,
                            toolbar: { show: false }
                        },
                        plotOptions: {
                            bar: { borderRadius: 4, horizontal: true, distributed: true }
                        },
                        dataLabels: { enabled: false },
                        xaxis: { categories: movieNames },
                        legend: { show: false }
                    };
                    const barChart = new ApexCharts($elMovies[0], barOptions);
                    barChart.render();
                }

                // Pie Chart - Studio Stats
                const $elStudios = $("#studioStatsChart");
                if ($elStudios.length && studioStats.length > 0) {
                    $elStudios.empty();
                    const studioNames = studioStats.map(s => s.name);
                    const studioTickets = studioStats.map(s => parseInt(s.total_tickets));

                    const pieOptions = {
                        series: studioTickets,
                        chart: { type: 'pie', height: 300 },
                        labels: studioNames,
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: { width: 200 },
                                legend: { position: 'bottom' }
                            }
                        }]
                    };
                    const pieChart = new ApexCharts($elStudios[0], pieOptions);
                    pieChart.render();
                }
            };

            renderCharts();
        });
    </script>
@endsection
