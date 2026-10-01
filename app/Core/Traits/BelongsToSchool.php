<?php

namespace App\Core\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchool
{
    /**
     * Boot trait to scope queries by school_id unless user is Super Admin.
     */
    protected static function bootBelongsToSchool(): void
    {
        static::creating(function ($model) {
            if (! $model->school_id && auth()->check() && isset(auth()->user()->school_id)) {
                $model->school_id = auth()->user()->school_id;
            }
        });

        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Super Admin has global access to all schools
                if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
                    return;
                }

                if (isset($user->school_id)) {
                    $builder->where($builder->getModel()->getTable() . '.school_id', $user->school_id);
                }
            }
        });
    }

    /**
     * Get the school associated with the model.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
