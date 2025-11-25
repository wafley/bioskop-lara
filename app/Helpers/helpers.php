<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

if (!function_exists('spaRender')) {
    function spaRender(Request $request, string $content, array $data = [])
    {
        if ($request->ajax()) {
            /** @var \Illuminate\View\View $view */
            $view = View::make($content, $data);
            $sections = $view->renderSections();

            return response()->json([
                'title' => $sections['title'] ?? '',
                'styles'  => $sections['styles'] ?? '',
                'breadcrumb' => $sections['breadcrumb'] ?? '',
                'content' => $sections['content'] ?? '',
                'modal' => $sections['modal'] ?? '',
                'scripts' => $sections['scripts'] ?? '',
            ]);
        } else {
            return view($content, $data);
        }
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $withTime = true)
    {
        if (!$date) {
            return null;
        }

        $bulanIndo = [
            1 => 'Jan',
            'Feb',
            'Mar',
            'Apr',
            'Mei',
            'Jun',
            'Jul',
            'Agu',
            'Sep',
            'Okt',
            'Nov',
            'Des'
        ];

        $timestamp = strtotime($date);
        $day   = date('d', $timestamp);
        $month = $bulanIndo[(int)date('m', $timestamp)];
        $year  = date('Y', $timestamp);
        $time  = date('H:i', $timestamp);

        return $withTime
            ? "{$day} {$month} {$year} {$time}"
            : "{$day} {$month} {$year}";
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug($model, string $value)
    {
        $slugBase = Str::slug($value);
        $slug = $slugBase;
        $count = 0;

        while ($model::where('slug', $slug)->when(isset($model->id), fn($q) => $q->where('id', '!=', $model->id))->exists()) {
            $count++;
            $slug = $slugBase . '-' . $count;
        }

        return $slug;
    }
}

if (!function_exists('formatDuration')) {
    function formatDuration($minutes, $style = 'text')
    {
        if (!$minutes || $minutes <= 0) {
            return $style === 'input' ? '' : '-';
        }

        $hours = floor($minutes / 60);
        $mins  = $minutes % 60;

        if ($style === 'input') {
            return sprintf('%02d:%02d', $hours, $mins);
        }

        $result = [];

        if ($hours > 0) {
            $result[] = $hours . ' Jam';
        }

        if ($mins > 0) {
            $result[] = $mins . ' Menit';
        }

        return implode(' ', $result);
    }
}
