@extends('_layouts.app')
@section('title', $title)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('styles')
    <style data-partial="1">
        .border-end-dashed {
            border-right: 2px dashed #dee2e6 !important;
        }

        .ticket-card {
            border-radius: 12px;
            overflow: hidden;
        }

        .ticket-cutout-top,
        .ticket-cutout-bottom {
            position: absolute;
            left: -15px;
            width: 30px;
            height: 30px;
            background-color: #f4f5f8;
            border-radius: 50%;
            z-index: 1;
        }

        .ticket-cutout-top {
            top: -15px;
        }

        .ticket-cutout-bottom {
            bottom: -15px;
        }

        .mini-ticket {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s ease-in-out;
        }

        .mini-ticket:hover {
            transform: translateY(-3px);
        }

        .ticket-stub {
            min-width: 100px;
            border-right: 2px dashed rgba(255, 255, 255, 0.6);
        }

        @media (max-width: 767px) {
            .border-end-dashed {
                border-right: none !important;
                border-bottom: 2px dashed #dee2e6 !important;
            }

            .ticket-cutout-top,
            .ticket-cutout-bottom {
                display: none;
            }

            .ticket-stub {
                min-width: 80px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">

            <div class="card custom-card mb-4 border-0 shadow-sm ticket-card">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-8 p-4 border-end-dashed">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-{{ $transaction->status_label->class }}">
                                    <i class="{{ $transaction->status_label->icon }} me-1"></i> {{ $transaction->status_label->text }}
                                </span>
                                <small class="text-muted fw-bold">BOARDING PASS BIOSKOP</small>
                            </div>

                            <div class="d-flex gap-4">
                                @if ($transaction->schedule->movie->poster)
                                    <img src="{{ $transaction->schedule->movie->poster }}" class="rounded shadow-sm" alt="Poster" style="width: 120px; object-fit: cover;">
                                @endif

                                <div class="flex-grow-1">
                                    <h3 class="fw-bold mb-1 text-uppercase">{{ $transaction->schedule->movie->title }}</h3>
                                    <p class="text-muted mb-3 small">
                                        <i class="bi bi-film"></i> {{ implode(', ', $transaction->schedule->movie->genre) }}
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-clock-history"></i> {{ $transaction->schedule->movie->duration }}
                                    </p>

                                    <div class="row g-2 mt-2">
                                        <div class="col-6">
                                            <span class="text-muted d-block" style="font-size: 11px;">SUTRADARA</span>
                                            <strong class="text-dark">{{ $transaction->schedule->movie->director }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <span class="text-muted d-block" style="font-size: 11px;">KAPASITAS STUDIO</span>
                                            <strong class="text-dark">{{ $transaction->schedule->studio->capacity }} Kursi</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 p-4 bg-light d-flex flex-column justify-content-center text-center position-relative">
                            <div class="ticket-cutout-top"></div>
                            <div class="ticket-cutout-bottom"></div>

                            <h5 class="fw-bold text-primary text-uppercase mb-1">
                                <i class="bi bi-door-open-fill"></i> {{ $transaction->schedule->studio->name }}
                            </h5>
                            <hr class="border-secondary border-dashed my-3">

                            <div class="mb-3">
                                <span class="text-muted d-block" style="font-size: 11px;">TANGGAL TAYANG</span>
                                <strong class="text-dark fs-6">{{ formatDate($transaction->schedule->show_date, false) }}</strong>
                            </div>

                            <div>
                                <span class="text-muted d-block" style="font-size: 11px;">WAKTU TAYANG</span>
                                <strong class="text-danger fs-5">{{ substr($transaction->schedule->start_time, 0, 5) }} -
                                    {{ substr($transaction->schedule->end_time, 0, 5) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Tiket ({{ $transaction->total_tickets }} Kursi)</h4>

                    <a href="{{ route('transactions.print.ticket', $transaction->invoice_number) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="bi bi-ticket-perforated me-1"></i> Print Tiket
                    </a>
                </div>

                <div class="card-body bg-light p-4">
                    <div class="row g-3">
                        @foreach ($transaction->tickets as $ticket)
                            @php
                                $isVip = $ticket->seat->type === 'vip';
                                $stubBg = $isVip ? 'bg-warning text-dark' : 'bg-primary text-white';
                                $badgeBg = $isVip ? 'bg-dark text-warning' : 'bg-white text-primary';
                            @endphp

                            <div class="col-md-6 col-xl-4">
                                <div class="card mini-ticket h-100 border-0 shadow-sm">
                                    <div class="d-flex h-100">
                                        <div class="{{ $stubBg }} ticket-stub d-flex flex-column justify-content-center align-items-center p-3">
                                            <span style="font-size: 9px; letter-spacing: 1px; opacity: 0.8;">KURSI</span>
                                            <h3 class="fw-bold mb-0 lh-1 mt-1">{{ $ticket->seat->seat_code }}</h3>
                                            <span class="badge {{ $badgeBg }} mt-2 shadow-sm" style="font-size: 0.65rem;">
                                                {{ strtoupper($ticket->seat->type) }}
                                            </span>
                                        </div>

                                        <div class="bg-white p-3 flex-grow-1 position-relative d-flex flex-column justify-content-center">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="text-muted d-block" style="font-size: 9px; letter-spacing: 0.5px;">KODE TIKET</span>
                                                    <code class="text-dark fw-bold" style="font-size: 13px;">{{ $ticket->ticket_code }}</code>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                                                <div>
                                                    <span class="text-muted d-block" style="font-size: 9px; letter-spacing: 0.5px;">HARGA</span>
                                                    <strong class="text-dark fs-6">{{ formatPrice($ticket->price) }}</strong>
                                                </div>
                                                <span
                                                    class="badge bg-{{ $ticket->status_label->class }} bg-opacity-10 text-{{ $ticket->status_label->class }} border border-{{ $ticket->status_label->class }}">
                                                    <i class="{{ $ticket->status_label->icon }}"></i> {{ $ticket->status_label->text }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card custom-card mb-4">
                <div class="card-header">
                    <h4 class="card-title">Informasi Transaksi</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block small">Nomor Invoice</label>
                        <span class="fw-bold fs-5">{{ $transaction->invoice_number }}</span>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block small">Tanggal Transaksi</label>
                        <span class="fw-semibold">{{ formatDate($transaction->created_at) }}</span>
                    </div>
                    <div>
                        <label class="text-muted d-block small">Kasir</label>
                        <span class="fw-semibold">
                            <i class="bi bi-person-circle text-muted me-1"></i> {{ $transaction->cashier->name ?? 'Sistem' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Rincian Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Metode Pembayaran</span>
                        <strong class="text-uppercase">{{ $transaction->payment_method_label }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Harga</span>
                        <strong>{{ formatPrice($transaction->total_price) }}</strong>
                    </div>

                    @if ($transaction->payment_method === 'cash')
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tunai Diterima</span>
                            <strong>{{ formatPrice($transaction->amount_paid) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Uang Kembalian</span>
                            <strong class="text-success">{{ formatPrice($transaction->change_amount) }}</strong>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-dark fw-bold fs-5">Total Net</span>
                        <span class="text-primary fw-bold fs-4">{{ formatPrice($transaction->total_price) }}</span>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('transactions.print.receipt', $transaction->invoice_number) }}" target="_blank" class="btn btn-outline-primary w-100 py-2 fw-bold">
                        <i class="bi bi-receipt me-2"></i> Print Struk
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
