<?php

namespace Alive2212\LaravelMobilePassport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use PeterPetrus\Auth\PassportToken;

class LaravelMobilePassport
{
    /**
     *
     */
    public static function initPassportTokenCan()
    {
        if(Schema::hasTable('alive_mobile_passport_roles')){
            $roles = new AliveMobilePassportRole();
            $scopes = [];
            foreach ($roles->get()->toArray() as $role) {
                $scopes = array_add($scopes, $role['title'], $role['description']);
            }
            Passport::tokensCan($scopes);
        }
    }

    /**
     * @param Request $request
     */
    public static function initUserInfo(Request $request)
    {
        $bearerToken = $request->bearerToken();
        $decodedBearerToken = PassportToken::dirtyDecode($bearerToken);
        $request['bearerTokenParams'] = $decodedBearerToken;
    }

    /**
     * @param Request $request
     */
    public static function initAccessToken(Request $request)
    {
        if (!isset($request['bearerTokenParams'])){
            self::initUserInfo($request);
        }
        $request['access_token'] = $accessToken =
            DB::table('oauth_access_tokens')
            ->where('id', $request['bearerTokenParams'])
            ->first();
    }
}