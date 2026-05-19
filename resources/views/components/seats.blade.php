@php $seats = $seats ?? []; @endphp

@php
    function seatButtonClass($seat, $isEdit = true, $isBooked = false)
    {
        $type = $seat->type;
        $status = $seat->status ?? true;

        $classes = ['btn', 'w-100', 'mx-1'];

        // Seat booked
        if ($isBooked) {
            $classes[] = $type === 'vip' ? 'btn-warning readonly' : 'btn-primary readonly';

            return implode(' ', $classes);
        }

        // Seat normal
        $classes[] = match ($type) {
            'vip' => $status ? 'btn-outline-warning' : 'btn-warning-light btn-border-down readonly',

            'disabled' => 'btn-outline-secondary readonly',

            default => $status ? 'btn-outline-primary' : 'btn-primary-light btn-border-down readonly',
        };

        if (!$isEdit) {
            $classes[] = 'readonly';
        }

        return implode(' ', $classes);
    }
@endphp

<div class="container-fluid">
    @foreach ($seats as $row => $seatRow)
        <div class="row mb-3">
            @foreach (['A', 'B', 'C'] as $section)
                <div class="{{ $section === 'B' ? 'col-lg-6' : 'col-lg-3' }} d-flex justify-content-center px-3">

                    @foreach (collect($seatRow)->where('section', $section) as $seat)
                        @php
                            $isBooked = isset($bookedSeatIds) && in_array($seat->id, $bookedSeatIds);

                            $classes = seatButtonClass($seat, true, $isBooked);
                        @endphp

                        <button type="button" class="{{ $classes }} seat-item" data-seat-id="{{ $seat->id }}" {{ $isBooked ? 'disabled' : '' }}>
                            {{ $seat->seat_code }}
                        </button>
                    @endforeach

                </div>
            @endforeach
        </div>
    @endforeach
</div>

<style>
    .readonly {
        pointer-events: none;
    }
</style>
