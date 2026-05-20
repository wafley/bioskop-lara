<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelper;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $fields = config('settings.fields');
        $settings = SettingHelper::all();

        $data = [
            'fields' => $fields,
            'settings' => $settings,
        ];

        return spaRender($request, 'settings.index', $data);
    }

    public function update(Request $request)
    {
        $fields = config('settings.fields');

        $rules = collect($fields)
            ->mapWithKeys(fn($field, $key) => [
                $key => $field['rules']
            ])
            ->toArray();

        $validated = $request->validate($rules);

        foreach ($validated as $key => $value) {
            SettingHelper::set($key, $value);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan berhasil diperbarui.',
            'redirect' => route('settings.index'),
            'redirect_type' => 'spa',
        ]);
    }
}
