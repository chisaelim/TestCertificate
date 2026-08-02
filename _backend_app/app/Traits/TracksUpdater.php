<?php

namespace App\Traits;

trait TracksUpdater
{
    public static function bootTracksUpdater(): void
    {
        static::updating(function ($model) {
            if ($model->shouldSkipTracksUpdater())
                return;

            $user = request()->user();
            if ($user) {
                $model->updated_by = $user->id;
                $model->updated_at = app('requested_at');
            }
        });

        if (property_exists(static::class, 'tracksUpdaterChildren')) {
            foreach (static::$tracksUpdaterChildren as $childClass => $config) {
                $foreignKey = is_array($config) ? $config['foreignKey'] : $config;
                $excludeKeys = is_array($config) ? ($config['excludeKeys'] ?? []) : [];
                static::registerChildObservers($childClass, $foreignKey, $excludeKeys);
            }
        }
    }

    protected function shouldSkipTracksUpdater(): bool
    {
        if (!property_exists(static::class, 'tracksUpdaterExcludeKeys'))
            return false;

        $dirty = array_keys($this->getDirty());
        $remaining = array_diff($dirty, static::$tracksUpdaterExcludeKeys);

        return empty($remaining);
    }

    protected static function touchParentRecord($child, string $foreignKey, string $parentClass): void
    {
        $user = request()->user();
        if (!$user)
            return;

        $parent = $parentClass::find($child->$foreignKey);
        if ($parent) {
            $parent->updated_by = $user->id;
            $parent->updated_at = app('requested_at');
            $parent->saveQuietly();
        }
    }

    protected static function registerChildObservers(string $childClass, string $foreignKey, array $excludeKeys = []): void
    {
        $parentClass = static::class;

        $touchParent = function ($child) use ($foreignKey, $parentClass) {
            static::touchParentRecord($child, $foreignKey, $parentClass);
        };

        $childClass::created($touchParent);
        $childClass::deleted($touchParent);

        $childClass::updated(function ($child) use ($foreignKey, $parentClass, $excludeKeys) {
            if (!empty($excludeKeys)) {
                $dirty = array_keys($child->getDirty());
                if (empty(array_diff($dirty, $excludeKeys)))
                    return;
            }

            static::touchParentRecord($child, $foreignKey, $parentClass);
        });
    }
}
