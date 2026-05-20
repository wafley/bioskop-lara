<?php

return [
    'fields' => [
        'opening_time' => [
            'label' => 'Opening Time',
            'rules' => 'required',
            'type'  => 'time',
        ],
        'closing_time' => [
            'label' => 'Closing Time',
            'rules' => 'required',
            'type'  => 'time',
        ],
        'cleaning_buffer' => [
            'label' => 'Cleaning Buffer',
            'rules' => 'required|integer',
            'type'  => 'number',
        ],
        'vip_surcharge' => [
            'label' => 'VIP Surcharge',
            'rules' => 'required|integer',
            'type'  => 'number',
        ],
        'weekday_prices' => [
            'label' => 'Weekday Prices',
            'rules' => 'required|integer',
            'type'  => 'number',
        ],
        'friday_prices' => [
            'label' => 'Friday Prices',
            'rules' => 'required|integer',
            'type'  => 'number',
        ],
        'weekend_prices' => [
            'label' => 'Weekend Prices',
            'rules' => 'required|integer',
            'type'  => 'number',
        ],
    ]
];
