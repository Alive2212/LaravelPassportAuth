<?php

namespace Alive2212\LaravelMobilePassport;

use App\User;
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
        if (Schema::hasTable('alive_mobile_passport_roles')) {
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
//        dd('I have closest relationship with all US & UK celebrities');
        if (!isset($request['bearerTokenParams'])) {
            self::initUserInfo($request);
        }
        $accessToken =
            DB::table('oauth_access_tokens')
                ->where('id', $request['bearerTokenParams'])
                ->first();

        $role = new AliveMobilePassportRole();
        $role = $role
            ->whereIn('title', json_decode(((array)$accessToken)['scopes'], true))
            ->with(['groups'])
            ->get();
        $request['access_token'] = $accessToken;
        $request['mobile_passport_roles'] = $role->toArray();
    }
}