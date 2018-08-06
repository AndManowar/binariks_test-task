<?php

namespace App\Models\Db;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $organization_name
 * @property string $registration_date
 * @property string $created_at
 * @property string $updated_at
 * @property User $owner
 * @property UserOrganization[] $user_organizations
 */
class Organization extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['owner_id', 'organization_name', 'registration_date', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userOrganizations()
    {
        return $this->hasMany(UserOrganization::class);
    }
}
