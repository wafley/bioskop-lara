@php $seats = $seats ?? []; @endphp
@php
    function seatButtonClass($seat, $isEdit)
    {
        $type = $seat->type;
        $status = $seat->status ?? true;

        $classes = ['btn'];

        $classes[] = match ($type) {
            'vip' => $status ? 'btn-outline-warning' : 'btn-warning-light btn-border-down readonly',
            'disabled' => 'btn-outline-secondary readonly',
            default => $status ? 'btn-outline-primary' : 'btn-primary-light btn-border-down readonly',
        };

        if (!$isEdit) {
            $classes[] = 'readonly';
        }

        $classes[] = 'w-100 mx-1';
        return implode(' ', $classes);
    }
@endphp

<div class="container-fluid">
    @foreach ($seats as $row => $seatRow)
        <div class="row mb-3">
            @foreach (['A', 'B', 'C'] as $section)
                <div class="{{ $section === 'B' ? 'col-lg-6' : 'col-lg-3' }} d-flex justify-content-center px-3">
                    @foreach (collect($seatRow)->where('section', $section) as $seat)
                        <button class="{{ seatButtonClass($seat, $isEdit) }}">
                            @if ($seat->type === 'disabled')
                                <i class="bi bi-tools"></i>
                            @else
                                {{ $seat->seat_code }}
                            @endif
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
