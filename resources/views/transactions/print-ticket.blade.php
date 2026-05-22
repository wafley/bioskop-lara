@extends('_layouts.print')
@section('title', 'Tiket Masuk #' . $transaction->invoice_number)

@push('styles')
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 76mm;
            margin: 0 auto;
            padding: 1rem auto;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .ticket-box {
            border: 1px solid #000;
            padding: 10px auto;
            margin-bottom: 20px;
            text-align: center;
            page-break-after: always;
        }

        .ticket-box:last-child {
            page-break-after: avoid;
        }
    </style>
@endpush

@section('content')
    @foreach ($transaction->tickets as $ticket)
        <div class="ticket-box">
            <h3 style="margin: 0 0 5px 0;">{{ config('app.name', 'Bioskop Lara') }}</h3>
            <div style="font-size: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px;">TIKET MASUK BIOSKOP</div>

            <div class="text-bold" style="font-size: 14px;">{{ $transaction->schedule->movie->title }}</div>
            <div>{{ $transaction->schedule->studio->name }}</div>
            <div>{{ formatDate($transaction->schedule->show_date, false) }} - Jam: {{ substr($transaction->schedule->start_time, 0, 5) }}</div>

            <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>

            <div style="font-size: 11px;">NOMOR KURSI</div>
            <div class="text-bold" style="font-size: 26px; margin: 2px 0;">{{ $ticket->seat->seat_code }}</div>
            <div style="font-size: 10px;">({{ strtoupper($ticket->seat->type) }})</div>

            <div style="border-top: 1px dashed #000; margin: 8px 0;"></div>
            <div style="font-size: 9px;">KODE AKSES: {{ $ticket->ticket_code }}</div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        window.onload = function() {
            window.print();
            window.close();
        }
    </script>
@endpush
