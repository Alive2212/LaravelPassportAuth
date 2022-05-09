<?php

namespace Alive2212\LaravelMobilePassport\Models;

use Alive2212\LaravelSmartRestful\BaseModel;
use App\User;

class AliveMobilePassportGroup extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'title',
        'subtitle',
        'description',
        'revoked',
    ];

    /**
     * @return null
     */
    public function getQueueableRelations()
    {
        return null;
        // TODO: Implement getQueueableRelations() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function authors()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            AliveMobilePassportRole::class,
            'alive_mobile_passport_group_role',
            'group_id',
            'roles_id');
    }
}