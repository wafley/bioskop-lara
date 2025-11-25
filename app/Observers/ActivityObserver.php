<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class ActivityObserver
{
    /**
     * Handle model "created" event.
     */
    public function created($model)
    {
        activity($this->getLogName($model))
            ->causedBy(Auth::user() ?? null)
            ->performedOn($model)
            ->withProperties($this->getProperties($model))
            ->event('created')
            ->log('Created');
    }

    /**
     * Handle model "updated" event.
     */
    public function updated($model)
    {
        $skipFields = [
            'remember_token',
        ];

        $dirty = $model->getDirty();
        if (
            !Auth::check() ||
            (!empty($dirty) && empty(array_diff(array_keys($dirty), $skipFields)))
        ) {
            return;
        }

        activity($this->getLogName($model))
            ->causedBy(Auth::user() ?? null)
            ->performedOn($model)
            ->withProperties($this->getProperties($model, 'updated'))
            ->event('updated')
            ->log('Updated');
    }

    /**
     * Handle model "deleted" event.
     */
    public function deleted($model)
    {
        activity($this->getLogName($model))
            ->causedBy(Auth::user() ?? null)
            ->performedOn($model)
            ->withProperties($this->getProperties($model))
            ->event('deleted')
            ->log('Deleted');
    }

    /**
     * Logging custom activity (login, logout, etc)
     */
    public function logCustom(string $message, string $event, string $logName = 'custom', array $properties = [])
    {
        activity($logName)
            ->causedBy(Auth::user() ?? null)
            ->withProperties($properties)
            ->event($event)
            ->log($message);
    }

    /**
     * Determine log name based on model class
     */
    protected function getLogName($model): string
    {
        $map = [
            \App\Models\Role::class => 'roles',
            \App\Models\User::class => 'users',
            \App\Models\Movie::class => 'movies',
        ];

        return $map[get_class($model)] ?? 'general';
    }

    /**
     * Add extra properties for logging
     */
    protected function getProperties($model, $event = null): array
    {
        $props = [
            'id' => $model->id ?? null,
            'attributes' => $model->getAttributes(),
        ];

        if ($event === 'updated') {
            $props['old'] = $model->getOriginal();
        }

        return $props;
    }
}
