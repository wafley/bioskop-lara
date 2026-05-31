@extends('_layouts.app')
@section('title', 'Dashboard Kasir')

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <!-- Welcome Header -->
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}! 👋</h4>
                            <p class="text-muted mb-0">Selamat bekerja! Berikut adalah ringkasan performa Anda hari ini.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('booking.index') }}" class="btn btn-primary spa-link">
                                <i class="bi bi-ticket-detailed me-1"></i> Jual Tiket
                            </a>
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary spa-link">
                                <i class="bi bi-clock-history me-1"></i> Riwayat Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Pendapatan Hari Ini</h6>
                        <h3 class="mb-0 fw-bold text-success">{{ formatPrice($todayRevenue) }}</h3>
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
                        <h6 class="text-muted mb-2">Tiket Terjual Hari Ini</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($todayTickets) }} <small class="fs-6 text-muted">Kursi</small></h3>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info rounded p-3">
                        <i class="bi bi-ticket-perforated fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card custom-card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-2">Transaksi Hari Ini</h6>
                        <h3 class="mb-0 fw-bold">{{ number_format($todayTransactions) }} <small class="fs-6 text-muted">Invoice</small></h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Jadwal Tayang Hari Ini -->
        <div class="col-12 col-lg-8">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Jadwal Tayang Hari Ini</h5>
                        <small class="text-muted">Jadwal film untuk tanggal {{ \Carbon\Carbon::today()->format('d M Y') }}</small>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light position-sticky top-0">
                                <tr>
                                    <th class="ps-4">Film</th>
                                    <th>Jadwal Tayang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaySchedules as $movie)
                                    <tr>
                                        <td class="ps-4 fw-bold align-top pt-3">
                                            {{ Str::limit($movie->title, 35) }}
                                        </td>
                                        <td class="pt-3 pb-2">
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($movie->schedules as $schedule)
                                                    @php
                                                        $isPast = \Carbon\Carbon::parse($schedule->start_time)->isPast();
                                                    @endphp
                                                    @if (!$isPast)
                                                        <a href="{{ route('booking.show', $schedule->uuid) }}" class="btn btn-sm btn-outline-primary spa-link mb-2"
                                                            title="{{ $schedule->studio->name }} - {{ formatPrice($schedule->price) }}">
                                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                        </a>
                                                    @else
                                                        <span class="btn btn-sm btn-outline-secondary disabled mb-2"
                                                            title="{{ $schedule->studio->name }} - {{ formatPrice($schedule->price) }}">
                                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} (Berakhir)
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted">Tidak ada jadwal tayang hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Terakhir Anda -->
        <div class="col-12 col-lg-4">
            <div class="card custom-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Transaksi Terakhir Anda</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-link spa-link">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentTransactions as $transaction)
                            <a href="{{ route('transactions.show', $transaction->id) }}" class="list-group-item list-group-item-action p-3 spa-link">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-primary">{{ $transaction->invoice_number }}</span>
                                    <span class="badge {{ $transaction->status == 'success' ? 'bg-success' : ($transaction->status == 'failed' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate me-2" style="max-width: 150px;">
                                        {{ $transaction->schedule->movie->title }}
                                    </small>
                                    <span class="fw-bold text-success">{{ formatPrice($transaction->total_price) }}</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-clock me-1"></i> {{ $transaction->created_at->diffForHumans() }}
                                </small>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <p class="mb-0">Belum ada transaksi hari ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
