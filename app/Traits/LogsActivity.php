<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    public function logActivity($action, $description = null)
    {
        // Get changes if updating
        $properties = null;
        if ($action === 'updated') {
            $properties = [
                'old' => array_intersect_key($this->getOriginal(), $this->getDirty()),
                'new' => $this->getDirty(),
            ];
            
            // Don't log if no meaningful changes
            if (empty($properties['new'])) return;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => $description ?? $this->getActivityDescription($action),
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    protected function getActivityDescription($action)
    {
        $name = class_basename($this);
        return "{$name} was {$action}";
    }
}
