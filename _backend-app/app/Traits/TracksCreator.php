<?php

namespace App\Traits;

trait TracksCreator
{
    public static function bootTracksCreator(): void
    {
        static::creating(function ($model) {
            $user = request()->user();
            if ($user) {
                $attrs = $model->getAttributes();
                if (empty($attrs['created_by'])) {
                    $model->created_by = $user->id;
                }
                if (empty($attrs['created_at'])) {
                    $model->created_at = app('requested_at');
                }
                if (empty($attrs['updated_by'])) {
                    $model->updated_by = $user->id;
                }
                if (empty($attrs['updated_at'])) {
                    $model->updated_at = app('requested_at');
                }
            }
        });
    }
}
