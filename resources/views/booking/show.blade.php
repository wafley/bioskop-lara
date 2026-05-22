@extends('_layouts.app')
@section('title', $title)

@section('breadcrumb')
    @include('_partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        @if ($schedule->movie->poster)
                            <img src="{{ $schedule->movie->poster }}" class="rounded shadow-sm" alt="Poster" width="160">
                        @endif

                        <div class="flex-grow-1 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-{{ $schedule->status_label->class }} mb-1">
                                        <i class="{{ $schedule->status_label->icon }} me-1"></i>
                                        {{ $schedule->status_label->text }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        <h2 class="fw-bold d-inline-block">
                                            {{ $schedule->movie->title }}
                                        </h2>
                                        <a href="{{ route('movies.show', $schedule->movie->slug) }}" class="text-primary">
                                            Detail <i class='bi bi-arrow-right'></i>
                                        </a>
                                    </div>
                                    <p class="text-muted">
                                        <i class="bi bi-tags me-1"></i>
                                        {{ implode(', ', $schedule->movie->genre) }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">Harga Tiket</small>
                                    <h4 class="fw-bold text-primary">
                                        {{ formatPrice($schedule->price) }}
                                    </h4>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap bg-light rounded shadow-sm py-3 g-3">
                                <div class="col-6 col-md-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Tanggal</div>
                                    <h5 class="fw-bold">{{ formatDate($schedule->show_date, false) }}</h5>
                                </div>
                                <div class="col-6 col-md-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Mulai</div>
                                    <h5 class="fw-bold text-success">{{ substr($schedule->start_time, 0, 5) }}</h5>
                                </div>
                                <div class="col-6 col-md-3 text-center border-end border-secondary">
                                    <div class="text-muted small">Selesai</div>
                                    <h5 class="fw-bold text-danger">{{ substr($schedule->end_time, 0, 5) }}</h5>
                                </div>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="text-muted small">Durasi</div>
                                    <h5 class="fw-bold">{{ $schedule->movie->duration }}</h5>
                                </div>
                            </div>

                            <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary w-100 mt-auto spa-link">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h4 class="card-title">Informasi Studio</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-door-open fs-1 text-primary mb-2"></i>
                        <h4 class="fw-bold mb-0">{{ $schedule->studio->name }}</h4>
                        <p class="text-muted mb-2">Kapasitas: {{ $schedule->studio->capacity ?? 'N/A' }} Kursi</p>
                        <a href="{{ route('studios.show', $schedule->studio->slug) }}" class="btn btn-sm btn-outline-secondary">
                            Detail <i class='bi bi-arrow-right'></i>
                        </a>
                    </div>
                    <x-seats :seats="$seats" :bookedSeatIds="$bookedSeatIds" :basePrice="$schedule->price" />
                </div>
            </div>
        </div>

        <div class="col-4">
            <form action="{{ route('transactions.store') }}" method="POST" data-ajax="true">
                @csrf
                @method('POST')

                <div class="card custom-card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Pemesanan</h4>
                    </div>
                    <div class="card-body pb-0">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Film:</span>
                            <span>{{ $schedule->movie->title }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Studio:</span>
                            <span>{{ $schedule->studio->name }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Kursi yang Dipilih:</span>
                            <span id="selected-seats-list">Belum memilih kursi</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Bayar:</span>
                            <strong id="total-price">Rp 0</strong>
                        </div>

                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        <div id="hidden-inputs-container"></div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Metode Pembayaran</label>
                            <select class="form-control" name="payment_method" id="payment_method">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank (QRIS/Debit)</option>
                            </select>
                        </div>

                        <div id="cash-payment-wrapper" class="mb-3">
                            <label for="amount_paid" class="form-label">Nominal Uang Diterima</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount_paid" id="amount_paid" class="form-control" placeholder="0" min="0">
                            </div>
                            <div class="form-text small" id="change-amount-label">Kembalian: Rp 0</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                        <a href="{{ route('movies.index') }}" class="btn btn-secondary spa-link">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script data-partial="1">
        $(document).ready(function() {
            // State holding elected seats
            let selectedSeats = [];

            // DOM caching selector
            const $seats = $('.seat-item');
            const $listDisplay = $('#selected-seats-list');
            const $totalDisplay = $('#total-price');
            const $hiddenContainer = $('#hidden-inputs-container');
            const $amountPaid = $('#amount_paid');
            const $changeLabel = $('#change-amount-label');
            const $paymentMethod = $('#payment_method');
            const $cashWrapper = $('#cash-payment-wrapper');

            // Rupiah formatter
            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            // Event: Select Seat
            $seats.on('click', function() {
                const $btn = $(this);

                // Change the appearance of the button (from outline to solid or vice versa)
                $btn.toggleClass('active btn-primary');

                const seatData = {
                    id: $btn.data('seat-id'),
                    code: $btn.data('seat-code'),
                    price: parseInt($btn.data('seat-price'))
                };

                if ($btn.hasClass('active')) {
                    selectedSeats.push(seatData);
                } else {
                    selectedSeats = selectedSeats.filter(s => s.id !== seatData.id);
                }

                updateUI();
            });

            // Event: Interaction Toggle Payment Method Options
            $paymentMethod.on('change', function() {
                if ($(this).val() === 'transfer') {
                    $cashWrapper.slideUp(200);
                    $amountPaid.prop('required', false).val('');
                } else {
                    $cashWrapper.slideDown(200);
                    $amountPaid.prop('required', true);
                }
                updateUI();
            });

            // Event: Input the nominal amount of cash received
            $amountPaid.on('input', function() {
                calculateChange();
            });

            // State Data Synchronization with Form UI
            function updateUI() {
                // 1. Selected Seat Badge Render
                if (selectedSeats.length > 0) {
                    const codes = selectedSeats.map(s => `<span class="badge bg-secondary me-1">${s.code}</span>`).join('');
                    $listDisplay.html(codes);
                } else {
                    $listDisplay.text('Belum memilih kursi');
                }

                // 2. Calculate Total Payment
                const total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
                $totalDisplay.text(formatRupiah(total));

                // 3. Rebuild Hidden Input to send to Backend via data-ajax="true"
                $hiddenContainer.empty();
                selectedSeats.forEach(seat => {
                    $hiddenContainer.append(`<input type="hidden" name="seat_ids[]" value="${seat.id}">`);
                });

                // 4. Update Return Label
                calculateChange(total);
            }

            // Calculate the cashier's remaining change
            function calculateChange(totalPrice = null) {
                const total = totalPrice ?? selectedSeats.reduce((sum, s) => sum + s.price, 0);
                const isCash = $paymentMethod.val() === 'cash';

                if (!isCash) {
                    $changeLabel.text('Kembalian: Rp 0').removeClass('text-danger text-success');
                    return;
                }

                const paid = parseInt($amountPaid.val()) || 0;
                const change = paid - total;

                if (paid > 0) {
                    $changeLabel.text('Kembalian: ' + formatRupiah(change));
                    $changeLabel.toggleClass('text-danger', change < 0);
                    $changeLabel.toggleClass('text-success', change >= 0);
                } else {
                    $changeLabel.text('Kembalian: Rp 0').removeClass('text-danger text-success');
                }
            }
        });
    </script>
@endsection
