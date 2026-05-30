@extends('_layouts.app')
@section('title', 'Laporan & Statistik')

@section('breadcrumb')
    @include('_partials.breadcrumb')
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
                                    <i class="bi bi-funnel me-2"></i> Terapkan Filter
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
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Tiket Terjual</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($total_tickets) }} <small class="fs-6 text-muted">Kursi</small></h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                        <i class="bi bi-ticket-perforated fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Total Transaksi</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($total_transactions) }} <small class="fs-6 text-muted">Invoice</small></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Tren Pendapatan & Penjualan Tiket Harian</h5>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Top 5 Film Terlaris</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Peringkat</th>
                                    <th>Judul Film</th>
                                    <th class="text-center">Tiket Terjual</th>
                                    <th class="text-end pe-4">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_movies as $index => $movie)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'light text-dark border') }} rounded-pill px-3">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="fw-bold">{{ $movie->title }}</td>
                                        <td class="text-center">{{ number_format($movie->tickets_sold) }}</td>
                                        <td class="text-end text-success fw-semibold pe-4">{{ formatPrice($movie->revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data penjualan pada rentang tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Metode Pembayaran</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    @if ($payment_methods->isEmpty())
                        <p class="text-muted">Belum ada data.</p>
                    @else
                        <div id="paymentChart" class="w-100"></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('templates/libs/apexcharts/apexcharts.min.js') }}" data-partial="1"></script>
    <script data-partial="1">
        $(document).ready(function() {
            // 1. DATA PREPARATION: Mixed Chart (Revenue & Tickets)
            const dailyData = @json($daily_data);
            const dates = dailyData.map(item => item.date);
            const revenues = dailyData.map(item => parseFloat(item.revenue));
            const tickets = dailyData.map(item => parseInt(item.tickets));

            // 2. MIXED CHART CONFIG (Bar for Tickets, Line for Revenue)
            const mixedOptions = {
                series: [{
                        name: 'Tiket Terjual',
                        type: 'column',
                        data: tickets
                    },
                    {
                        name: 'Pendapatan',
                        type: 'line',
                        data: revenues
                    }
                ],
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
                stroke: {
                    width: [0, 3], // 0 for column, 3 for line
                    curve: 'smooth'
                },
                colors: ['#0dcaf0', '#0d6efd'], // Info for tickets, Primary for revenue
                dataLabels: {
                    enabled: true,
                    enabledOnSeries: [1], // Only show datalabels on Line
                    formatter: function(val) {
                        if (val === 0) return '';
                        return 'Rp ' + (val / 1000000).toFixed(1) + 'M'; // Format to Millions for cleaner look
                    }
                },
                xaxis: {
                    type: 'datetime',
                    categories: dates
                },
                yaxis: [{
                        title: {
                            text: 'Jumlah Tiket (Kursi)'
                        },
                        labels: {
                            formatter: val => parseInt(val)
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Pendapatan (Rupiah)'
                        },
                        labels: {
                            formatter: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y, {
                            seriesIndex
                        }) {
                            if (typeof y !== "undefined") {
                                if (seriesIndex === 0) return parseInt(y) + " Tiket";
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(y);
                            }
                            return y;
                        }
                    }
                }
            };

            // 3. DATA PREPARATION: Donut Chart (Payment Methods)
            const paymentData = @json($payment_methods);
            const paymentLabels = paymentData.map(item => item.payment_method.toUpperCase());
            const paymentSeries = paymentData.map(item => parseFloat(item.revenue));

            const paymentOptions = {
                series: paymentSeries,
                labels: paymentLabels,
                chart: {
                    type: 'donut',
                    height: 300
                },
                colors: ['#198754', '#ffc107', '#0dcaf0'], // Custom colors mapping
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            };

            // 4. RENDER CHARTS
            function renderCharts() {
                if (typeof ApexCharts !== 'undefined') {
                    // Render Main Mixed Chart
                    const revenueEl = document.querySelector("#revenueChart");
                    if (revenueEl) {
                        revenueEl.innerHTML = '';
                        new ApexCharts(revenueEl, mixedOptions).render();
                    }

                    // Render Donut Chart
                    const paymentEl = document.querySelector("#paymentChart");
                    if (paymentEl && paymentSeries.length > 0) {
                        paymentEl.innerHTML = '';
                        new ApexCharts(paymentEl, paymentOptions).render();
                    }
                } else {
                    setTimeout(renderCharts, 100);
                }
            }

            renderCharts();
        });
    </script>
@endsection
