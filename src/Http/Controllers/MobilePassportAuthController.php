<?php

namespace Alive2212\LaravelMobilePassport\Http\Controllers;

use Alive2212\LaravelMobilePassport\AliveMobilePassportDevice;
use Alive2212\LaravelMobilePassport\AliveMobilePassportRole;
use Alive2212\LaravelMobilePassport\Http\Requests\CreateThirdPartyUserToken;
use Alive2212\LaravelMobilePassport\LaravelMobilePassportSingleton;
use Alive2212\LaravelSmartResponse\ResponseModel;
use Alive2212\LaravelSmartResponse\SmartResponse;
use Alive2212\LaravelSmartRestful\BaseController;
use App\User;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;

class MobilePassportAuthController extends BaseController
{
    /**
     * @var Repository|mixed
     */
    protected $defaultUsers;

    /**
     * @var string
     */
    protected $defaultPassword = 'MniBN&IhmPowerFm!Dokhan$2018';

    /**
     * @var int
     */
    protected $otpTokenExpireTime = 5 * 60;

    /**
     * @var
     */
    protected $user;

    /**
     * @var
     */
    protected $otpToken;

    /**
     * @var
     */
    protected $device;

    /**
     * @var array
     */
    protected $storeValidateArray = [
        'scope' => 'required',
    ];

    /**
     * @var object
     */
    protected $token;

    /**
     * @var array
     */
    protected $registerByPasswordValidateArray = [
        'country_code' => 'required',
        'phone_number' => 'required',
//        'email' => 'required|email',
        'password' => 'required',
//        'imei' => 'required',
//        'app_name' => 'required',
//        'app_version' => 'required',
//        'platform' => 'required',
//        'os' => 'required',
//        'push_token' => 'required',
    ];

    /**
     * @var array
     */
    protected $otpConfirmValidateArray = [
        'country_code' => 'required',
        'phone_number' => 'required',
        'token' => 'required',
    ];

    /**
     * @var array
     */
    protected $registerByOtpValidateArray = [
        'country_code' => 'required',
        'phone_number' => 'required',
        'imei' => 'required',
        'app_name' => 'required',
        'app_version' => 'required',
        'platform' => 'required',
        'os' => 'required',
        'push_token' => 'required',
    ];

    /**
     * MobilePassportAuthController constructor.
     */
    public function __construct()
    {
        //config default users
        $this->defaultUsers = config('laravel-mobile-passport.default_users');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function credentialStore(Request $request)
    {
        // create response model
        $response = new ResponseModel();

        // check validation
        $validationErrors = $this->checkRequestValidation($request, $this->storeValidateArray);
        if (!is_null($validationErrors)) {
            if ($validationErrors != null) {
                if (env('APP_DEBUG', false)) {
                    $response->setData(collect($validationErrors->toArray()));
                }
                $response->setMessage($this->getTrans(__FUNCTION__, 'validation_failed'));
                $response->setStatusCode(403);
                return SmartResponse::response($response);
            }
        }

        // get scope
        if ($request->has('scope')) {
            $scope = $request['scope'];
        } else {
            $response->setMessage($this->getTrans(__FUNCTION__, 'scope_filed_failed'));
            $response->setStatusCode(404);
            return SmartResponse::response($response);
        }

        // get query in roles
        $role = new AliveMobilePassportRole();
        $role = $role->where('title', $scope)->first();

        // check it to exist
        if (is_null($role)) {
            $response->setMessage($this->getTrans(__FUNCTION__, 'scope_not_found'));
            $response->setStatusCode(404);
            return SmartResponse::response($response);
        }

        // is OTP
        if ($role['is_otp']) {

            $this->user = $this->firstOrCreateUser($request, true);

            $this->device = $this->firstOrCreateDevice($request);

            $role = new AliveMobilePassportRole();
            $role = $role->where('title', $request['scope'])->first();

            // assign role to user
            if (!is_null($role)) {
                $this->user->roles()->detach($role->id);
                $this->user->roles()->attach($role->id);
            }

            // put scope to request
            $request['scope'] = $scope;

            return $this->IssueToken($request);

        } else { // is password auth
            $response->setMessage($this->getTrans(__FUNCTION__, 'token_not_valid'));
            $response->setStatusCode(403);
            return SmartResponse::response($response);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        // create response model
        $response = new ResponseModel();

        // check validation
        $validationErrors = $this->checkRequestValidation($request, $this->storeValidateArray);
        if (!is_null($validationErrors)) {
            if ($validationErrors != null) {
                if (env('APP_DEBUG', false)) {
                    $response->setData(collect($validationErrors->toArray()));
                }
                $response->setMessage($this->getTrans(__FUNCTION__, 'validation_failed'));
                $response->setStatusCode(422);
                return SmartResponse::response($response);
            }
        }

        // get scope
        if ($request->has('scope')) {
            $scopes = json_decode($request->get('scope'));
            if (is_null($scopes)) {
                $scopes = $request->get('scope');
            }
        } else {
            $response->setMessage($this->getTrans(__FUNCTION__, 'scope_not_found'));
            $response->setStatusCode(404);
            return SmartResponse::response($response);
        }

        // get query in roles
        $roles = new AliveMobilePassportRole();
        $roles = $roles->whereIn('title', array($scopes))->get();

        // check it to exist
        if (count($roles->toArray()) == 0) {
            $response->setMessage($this->getTrans(__FUNCTION__, 'scope_not_found'));
            $response->setStatusCode(404);
            return SmartResponse::response($response);
        }

        $isOTP = true;

        foreach ($roles as $role) {
            if (!$role["is_otp"]) {
                $isOTP = false;
                break;
            }
        }

        // is OTP
        if ($isOTP) {
            return $this->registerByOtp($request);
        } else { // is password auth
            return $this->registerByPassword($request);
        }

    }

    /**
     * @param Request $request
     * @return AliveMobilePassportDevice
     */
    public function firstOrCreateDevice(Request $request)
    {
        // Attributes can overwritten by developer
        if (!isset($attributes)) {
            $attributes = [
                'user_id' => $this->user->id,
                'imei' => $request['imei'],
                'app_name' => $request['app_name'],
                'app_version' => $request['app_version'],
                'platform' => $request['platform'],
                'os' => $request['os'],
            ];
        }

        // Values can overwritten by developer
        if (!isset($values)) {
            $values = [
                // TODO read from app setting
                'push_token' => $request['push_token'],
            ];
        }

        $device = new AliveMobilePassportDevice();
        $device = $device->firstOrCreate($attributes, $values);
        return $device;
    }

    /**
     * this method can overwritten by developer
     *
     * @param Request $request
     * @param bool $isOtp
     * @return User
     */
    public function firstOrCreateUser(Request $request, bool $isOtp = false)
    {
        // Attributes can overwritten by developer
        if (!isset($attributes)) {
            $attributes = [
                'country_code' => $request['country_code'],
                'phone_number' => $request['phone_number'],
            ];
        }

        // Values can overwritten by developer
        if (!isset($values)) {
            $values = [
                // TODO read from app setting
                'email' => $request->has('email') ?
                    $request['email'] :
                    '',

                'name' =>
                    $request->has('name') ?
                        $request['name'] :
                        '',

                'password' => md5(
                    $request->has('password') ?
                        $request['password'] :
                        $this->defaultPassword
                ),
            ];
        }

        $user = new User();
        $user = $user->firstOrCreate($attributes, $values)->load('roles');
        return $user;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerByOtp(Request $request)
    {
        // create response model
        $response = new ResponseModel();
        // check validation
        $validationErrors = $this->checkRequestValidation($request, $this->registerByOtpValidateArray);
        if (!is_null($validationErrors)) {
            if ($validationErrors != null) {
                if (env('APP_DEBUG', false)) {
                    $response->setData(collect($validationErrors->toArray()));
                }
                $response->setMessage($this->getTrans(__FUNCTION__, 'validation_failed'));
                $response->setStatusCode(400);
                return SmartResponse::response($response);
            }
        }
        $this->user = $this->firstOrCreateUser($request, true);
        $this->device = $this->firstOrCreateDevice($request);
        $request = $this->generateOtpToken($request);
        $this->otpToken = $request['token'];
        call_user_func(
            LaravelMobilePassportSingleton::$otpCallBack,
            $request,
            $this->user,
            $this->device,
            $this->otpToken
        );
        if (env('APP_DEBUG')) {
            $response->setData(collect([
                'user' => $this->user->toArray(),
                'device' => $this->device->toArray(),
                'otp_token' => $this->otpToken,
            ]));
        }
        $response->setMessage($this->getTrans(__FUNCTION__, 'successful'));
        return SmartResponse::response($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmOtp(Request $request)
    {
        $response = new ResponseModel();

        // check validation
        $validationErrors = $this->checkRequestValidation($request, $this->otpConfirmValidateArray);
        if (!is_null($validationErrors)) {
            if ($validationErrors != null) {
                if (env('APP_DEBUG', false)) {
                    $response->setData(collect($validationErrors->toArray()));
                }
                $response->setMessage($this->getTrans(__FUNCTION__, 'validation_failed'));
                $response->setStatusCode(400);
                return SmartResponse::response($response);
            }
        }

        if ($this->tokenIsValid($request)) {
            // Successful
            $this->user = (new User())->where([
                ['phone_number', '=', $request['phone_number']],
                ['country_code', '=', $request['country_code']],
            ])->first();

            // scope key for cache scopes
            $scopeKey = $this->otpKeyMaker($request, 'scope');
            $jsonEncodedScope = Cache::get($scopeKey);
            $scopes = json_decode($jsonEncodedScope, true);
            if (is_null($scopes)) {
                $scopes = $jsonEncodedScope;
            }
            $roles = new AliveMobilePassportRole();
            $roles = $roles->whereIn('title', array($scopes))->first();

            foreach ($roles as $role) {
                // assign role to user
                if (!is_null($roles)) {
                    $this->user->roles()->detach($roles->id);
                    $this->user->roles()->attach($roles->id);
                }
            }

            // put scope to request
            $request['scope'] = $jsonEncodedScope;

            $result = $this->IssueToken($request);

            call_user_func(
                LaravelMobilePassportSingleton::$otpConfirmCallBack,
                $request,
                $this->user,
                $this->token
            );

            return $result;
        } else {
            // not Successful
            $response->setMessage($this->getTrans(__FUNCTION__, 'token_failed'));
            $response->setStatusCode(401);
            return SmartResponse::response($response);
        }
    }

    public function tokenIsValid(Request $request)
    {
        $cachedToken = Cache::get($this->otpKeyMaker($request, 'token'));
        return $cachedToken == $request['token'];
    }

    /**
     * @param Request $request
     * @return int|mixed
     */
    public function generateOtpToken(Request $request)
    {
        $tokenKey = $this->otpKeyMaker($request, 'token');
        $scopeKey = $this->otpKeyMaker($request, 'scope');

        // check for default users
        foreach ($this->defaultUsers as $defaultUser) {
            if (
                $defaultUser['country_code'] == $request['country_code'] &&
                $defaultUser['phone_number'] == $request['phone_number']
            ) {
                Cache::put($scopeKey, $request['scope'], $this->otpTokenExpireTime);
                Cache::put($tokenKey, $defaultUser['token'], $this->otpTokenExpireTime);
                $request['default_user'] = 1;
                $request['token'] = $defaultUser['token'];
                return $request;
            }
        }
        $cachedTokenKey = Cache::get($tokenKey);
        if (is_null($cachedTokenKey)) {
            // not any cached token

            $token = rand(1000, 9999);
            Cache::put($scopeKey, $request['scope'], $this->otpTokenExpireTime);
            Cache::put($tokenKey, $token, $this->otpTokenExpireTime);
        } else {

            $token = Cache::get($tokenKey);
        }
        $request['isDefaultUser'] = 1;
        $request['token'] = $token;
        return $request;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registerByPassword(Request $request)
    {
        // create response model
        $response = new ResponseModel();

        // check validation
        $validationErrors = $this->checkRequestValidation($request, $this->registerByPasswordValidateArray);
        if (!is_null($validationErrors)) {
            if ($validationErrors != null) {
                if (env('APP_DEBUG', false)) {
                    $response->setData(collect($validationErrors->toArray()));
                }
                $response->setMessage($this->getTrans(__FUNCTION__, 'validation_failed'));
                $response->setStatusCode(422);
                return SmartResponse::response($response);
            }
        }

        $this->user = $this->firstOrCreateUser($request);

        if (is_null($this->user)) {
            $response->setMessage($this->getTrans(__FUNCTION__, 'email_not_found'));
            $response->setStatusCode(404);
            return SmartResponse::response($response);
        }

        //check for user roles
        if (!$this->userHavePermission($request)) {
            $response->setMessage($this->getTrans(__FUNCTION__, 'permission_failed'));
            $response->setStatusCode(403);
            return SmartResponse::response($response);
        }

//        dd("hello");

        $this->firstOrCreateDevice($request);

        if (Hash::check($request->get("password"), $this->user->password)) {
            // Successful
            return $this->IssueToken($request);
        } else {
            // not Successful
            $response->setMessage($this->getTrans(__FUNCTION__, 'user_or_password_failed'));
            $response->setStatusCode(403);
            return SmartResponse::response($response);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function IssueToken(Request $request): JsonResponse
    {
        $scopes = json_decode($request->get("scope"), true);
        if (is_null($scopes)) {
            $scopes = $request->get("scope");
        }
//        $user = new User();
//        dd($scopes);
//        dd($this->user->createToken("Personal OTP Token", ['place-orders']));
//        dd($this->user->createToken("Personal OTP Token", ($scopes)));
//        dd('here');
        $this->token = $this->user->createToken("Personal OTP Token", $scopes);
        // response object
        $response = new ResponseModel();

        $response->setData(collect([
            'user_id' => $this->token->token->user_id,
            'scope' => $request->scope,
            'access_token' => $this->token->toArray()['accessToken'],
            'expires_at' => $this->token->token->expires_at->timestamp,
        ]));
        $response->setMessage($this->getTrans(__FUNCTION__, 'successful'));

        return SmartResponse::response($response);
    }

    /**
     *
     */
    public function initController()
    {
    }

    /**
     * @param Request $request
     * @param string $prefix
     * @return string
     */
    public function otpKeyMaker(Request $request, string $prefix = '')
    {
        $key = 'alive_mobile_passport_' .
            ($prefix == '' ? '' : $prefix . '_') .
            $request->get('country_code') .
            $request->get('phone_number');
        return $key;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function userHavePermission(Request $request): bool
    {
        $scopesParams = json_decode($request['scope'], true);
        $secureRequestedScopes = AliveMobilePassportRole::whereIn("title", $scopesParams)->where("is_otp", false)->pluck("title")->toArray();
        $userCurrentRoles = $this->user->roles->where("is_otp", false)->pluck("title")->toArray();

        foreach ($secureRequestedScopes as $secureRequestedScope) {
            if (!in_array($secureRequestedScope, $userCurrentRoles)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param CreateThirdPartyUserToken $request
     * @return JsonResponse
     */
    public function createThirdPartyUserToken(CreateThirdPartyUserToken $request): JsonResponse
    {
        $this->user = $this->firstOrCreateUser($request);
        $response = $this->IssueToken($request);

        $requestClient = Client::find($request->client_id);
        if (is_null($requestClient)) {
            return response()->json([
                "message" => "client id does not exist",
            ], 404);
        }
        if ($requestClient->secret != $request->client_secret) {
            return response()->json([
                "message" => "client secret not valid",
            ], 403);
        }

        $accessToken = $this->token->toArray()['accessToken'];
        $tokenId = (new Parser())->parse($accessToken)->getHeader('jti');
        $token = Token::find($tokenId);
        $token->client_id = $requestClient->id;
        $token->save();

        return $response;
    }
}
