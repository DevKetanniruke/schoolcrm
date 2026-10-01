<?php

namespace App\Core\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToOrganization
{
    /**
     * Boot trait to scope queries by organization_id unless user is Super Admin.
     */
    protected static function bootBelongsToOrganization(): void
    {
        static::creating(function ($model) {
            if (! $model->organization_id && auth()->check()) {
                $user = auth()->user();
                if (isset($user->organization_id)) {
                    $model->organization_id = $user->organization_id;
                } elseif (isset($user->school) && isset($user->school->organization_id)) {
                    $model->organization_id = $user->school->organization_id;
                }
            }
        });

        static::addGlobalScope('organization', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Super Admin has global bypass across all organizations
                if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
                    return;
                }

                $orgId = $user->organization_id ?? ($user->school->organization_id ?? null);
                if ($orgId) {
                    $builder->where($builder->getModel()->getTable() . '.organization_id', $orgId);
                }
            }
        });
    }

    /**
     * Get the organization associated with the model.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
