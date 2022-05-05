<?php

namespace Alive2212\LaravelMobilePassport;

use Alive2212\LaravelSmartRestful\BaseModel;
use App\User;

class AliveMobilePassportRole extends BaseModel
{
    /**
     * @var string[]
     */
    public $uniqueFields = ['key'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'key',
        'title',
        'subtitle',
        'description',
        'level',
        'is_otp',
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
    public function groups()
    {
        return $this->belongsToMany(
            AliveMobilePassportGroup::class,
            'alive_mobile_passport_group_role',
            'role_id',
            'group_id');
    }
}