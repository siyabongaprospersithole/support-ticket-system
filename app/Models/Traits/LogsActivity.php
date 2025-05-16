<?php

namespace App\Models\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->recordActivity('created', 'Created new ' . class_basename($model));
        });

        static::updated(function ($model) {
            if ($model->wasChanged()) {
                $changes = $model->getChanges();
                unset($changes['updated_at']);
                
                if (!empty($changes)) {
                    $description = 'Updated ' . class_basename($model);
                    $model->recordActivity('updated', $description, ['changes' => $changes]);
                }
            }
        });

        static::deleted(function ($model) {
            $model->recordActivity('deleted', 'Deleted ' . class_basename($model));
        });
    }

    /**
     * Get all activities for this model.
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    /**
     * Record an activity for this model.
     */
    public function recordActivity(string $type, string $description, array $properties = []): void
    {
        // Get causer information
        $causerId = auth()->id();
        $causerType = auth()->user() ? get_class(auth()->user()) : null;
        
        // If no authenticated user but model has user_id, use that instead (for seeding)
        if (!$causerId && isset($this->user_id)) {
            $causerId = $this->user_id;
            $causerType = config('auth.providers.users.model');
        }
        
        $this->activities()->create([
            'type' => $type,
            'description' => $description,
            'properties' => $properties,
            'causer_id' => $causerId,
            'causer_type' => $causerType,
        ]);
    }
} 