@extends('_layouts.app')
@section('title', 'Cetak Dokumen #' . $transaction->invoice_number)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                    <h3 class="fw-bold">Transaksi Berhasil!</h3>
                    <p class="text-muted">Nomor Invoice: <strong class="text-dark">{{ $transaction->invoice_number }}</strong></p>

                    <hr />

                    <div class="d-grid gap-3">
                        <a href="{{ route('transactions.print.receipt', $transaction->invoice_number) }}" target="_blank" class="btn btn-lg btn-outline-info">
                            <i class="bi bi-receipt me-2"></i> Cetak Struk Pembayaran
                        </a>

                        <a href="{{ route('transactions.print.ticket', $transaction->invoice_number) }}" target="_blank" class="btn btn-lg btn-primary">
                            <i class="bi bi-ticket-perforated me-2"></i> Cetak Tiket Masuk ({{ $transaction->total_tickets }} Kursi)
                        </a>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.index') }}" class="btn btn-light w-100 spa-link">
                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard Kasir
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
