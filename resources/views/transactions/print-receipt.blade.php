@extends('_layouts.print')
@section('title', 'Struk Pembayaran #' . $transaction->invoice_number)

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
            padding: 10px;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .text-bold {
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="text-center">
        <h3 style="margin: 0 0 5px 0; text-transform: uppercase;">{{ config('app.name', 'CINEPOLIS LARA') }}</h3>
        <p style="margin: 0; font-size: 10px;">STRUK BUKTI PEMBAYARAN RESMI</p>
    </div>
    <div class="divider"></div>
    <table style="width: 100%;">
        <tr>
            <td>Nota</td>
            <td>: {{ $transaction->invoice_number }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>: {{ $transaction->cashier->name ?? 'Kasir' }}</td>
        </tr>
    </table>
    <div class="divider"></div>
    <div class="text-center text-bold" style="font-size: 13px;">{{ $transaction->schedule->movie->title }}</div>
    <div class="text-center">{{ $transaction->schedule->studio->name }}</div>
    <div class="divider"></div>
    <table style="width: 100%;">
        <tr>
            <td>Tiket Bioskop x{{ $transaction->total_tickets }}</td>
            <td class="text-right">{{ formatPrice($transaction->total_price) }}</td>
        </tr>
        <tr class="text-bold">
            <td>TOTAL NET</td>
            <td class="text-right">{{ formatPrice($transaction->total_price) }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td class="text-right" style="text-transform: uppercase;">{{ $transaction->payment_method }}</td>
        </tr>
        @if ($transaction->payment_method === 'cash')
            <tr>
                <td>Bayar</td>
                <td class="text-right">{{ formatPrice($transaction->amount_paid) }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="text-right">{{ formatPrice($transaction->change_amount) }}</td>
            </tr>
        @endif
    </table>
    <div class="divider"></div>
    <div class="text-center" style="margin-top: 15px; font-size: 10px;">Terima kasih atas pembayaran Anda.</div>
@endsection

@push('scripts')
    <script>
        window.onload = function() {
            window.print();
            window.close();
        }
    </script>
@endpush
