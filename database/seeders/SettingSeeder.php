<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'opening_time',
                'value' => '10:00',
            ],
            [
                'key' => 'closing_time',
                'value' => '22:00',
            ],
            [
                'key' => 'cleaning_buffer',
                'value' => '20',
            ],
            [
                'key' => 'vip_surcharge',
                'value' => '10000',
            ],
            [
                'key' => 'weekday_prices',
                'value' => '40000',
            ],
            [
                'key' => 'friday_prices',
                'value' => '50000',
            ],
            [
                'key' => 'weekend_prices',
                'value' => '65000',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
