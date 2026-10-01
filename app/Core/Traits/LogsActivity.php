<?php

namespace App\Core\Traits;

use App\Core\Services\AuditLogger;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            app(AuditLogger::class)->log(
                action: strtolower(class_basename($model)).'.created',
                entityType: get_class($model),
                entityId: $model->getKey(),
                payload: $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            app(AuditLogger::class)->log(
                action: strtolower(class_basename($model)).'.updated',
                entityType: get_class($model),
                entityId: $model->getKey(),
                payload: [
                    'changes' => $model->getChanges(),
                    'original' => array_intersect_key($model->getOriginal(), $model->getChanges()),
                ]
            );
        });

        static::deleted(function ($model) {
            app(AuditLogger::class)->log(
                action: strtolower(class_basename($model)).'.deleted',
                entityType: get_class($model),
                entityId: $model->getKey(),
                payload: ['id' => $model->getKey()]
            );
        });
    }
}
