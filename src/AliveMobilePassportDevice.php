<?php

namespace Alive2212\LaravelMobilePassport;

use Alive2212\LaravelSmartRestful\BaseModel;
use App\User;

class AliveMobilePassportDevice extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'user_id',
        'imei',
        'app_name',
        'app_version',
        'platform',
        'os',
        'push_token',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}