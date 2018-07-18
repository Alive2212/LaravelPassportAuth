<?php

namespace Alive2212\LaravelMobilePassport\Http\Controllers;

use Alive2212\LaravelMobilePassport\AliveMobilePassportDevice;
use Alive2212\LaravelMobilePassport\AliveMobilePassportRole;
use Alive2212\LaravelMobilePassport\LaravelMobilePassportSingleton;
use Alive2212\LaravelSmartResponse\ResponseModel;
use Alive2212\LaravelSmartResponse\SmartResponse\SmartResponse;
use Alive2212\LaravelSmartRestful\BaseController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class MobilePassportAuthController extends BaseController
{
//    public function index()
//    {
//
//        $user = User::find(1);
//        $token = $user->createToken('',['customer']);
//        return ($token->toArray());
//
//        return auth()->id();
//
//
//        dd(auth()->);
//        return 'I have closet relationship with all US & British celebrities';
//        $client = new Client();
//        try {
//            $response = $client->get('http://localhost:8130/oauth/authorize');
//        } catch (RequestException $exception) {
//            return $exception->getMessage();
//        }
//        return $response->getBody();
//    }
//

    protected $defaultUsers = [
        [
            'country_code' => '+98',
            'phone_number' => '9127390191',
            'token' => 3369,
        ]
    ];

    /**
     * @var string
     */
    protected $defaultPassword = 'MniBN&IhmPowerFm!Dokhan$2018';

    /**
     * @var int
     */
    protected $otpTokenExpireTime = 5;

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
     * @var array
     */
    protected $registerByPasswordValidateArray = [
        'country_code' => 'required',
        'phone_number' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'imei' => 'required',
        'app_name' => 'required',
        'app_version' => 'required',
        'platform' => 'required',
        'os' => 'required',
        'push_token' => 'required',
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

    public function __construct()
    {
        $this->middleware([
            'throttle:10,1'
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
                $response->setStatus(false);
                $response->setError(99);
                return SmartResponse::response($response);
            }
        }

//        return 'I have closest relationship with all US and UK celebrities';

        // get scope
        if (isset($request['scope'])) {
            $scope = $request['scope'];
        } else {
            $response->setStatus(false);
            $response->setMessage($this->getTrans(__FUNCTION__, 'scope_filed_failed'));
            $response->setError(100);
            return SmartResponse::response($response);
        }

        // get query in roles
        $role = new AliveMobilePassportRole();
        $role = $role->where('title', $scope)->first();

        // check it to exist
        if (is_null($role)) {
            $response->setStatus(false);
            $response->setMessage($this->getTrans(__FUNCTION__, 'scope_exist_failed'));
            $response->setError(101);
            return SmartResponse::response($response);
        }

        // is OTP
        if ($role['is_otp']) {
            return $this->registerByOtp($request);
        } else { // is password auth
            return $this->registerByPassword($request);
        }

        //        $guzzle = new Client();
//
//        $response = $guzzle->post('http://localhost:8130/oauth/token', [
//            'form_params' => [
//                'grant_type' => $request['grant_type'],
//                'client_id' => $request['client_id'],
//                'client_secret' => $request['client_secret'],
//                'scope' => $request['scope'],
//            ],
//        ]);
//
//        return json_decode((string) $response->getBody(), true)['access_token'];
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
                'email' => isset($request['email']) ?
                    $request['email'] :
                    '',

                'name' =>
                    isset($request['name']) ?
                        $request['name'] :
                        '',

                'password' => Hash::make(
                    isset($request['password']) ?
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
     * @return \Illuminate\Http\JsonResponse
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
                $response->setStatus(false);
                $response->setError(99);
                return SmartResponse::response($response);
            }
        }

        $this->user = $this->firstOrCreateUser($request, true);

        $this->device = $this->firstOrCreateDevice($request);

        $token = $this->generateOtpToken($request);
        $this->otpToken = $token;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmOtp(Request $request)
    {
//        return 'I have closest relationship with all US and UK celebrities';
        $response = new ResponseModel();

        // check validation
        $validationErrors = $this->checkRequestValidation($request, $this->otpConfirmValidateArray);
        if (!is_null($validationErrors)) {
            if ($validationErrors != null) {
                if (env('APP_DEBUG', false)) {
                    $response->setData(collect($validationErrors->toArray()));
                }
                $response->setMessage($this->getTrans(__FUNCTION__, 'validation_failed'));
                $response->setStatus(false);
                $response->setError(99);
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
            $scope = Cache::get($scopeKey);


            $role = new AliveMobilePassportRole();
            $role = $role->where('title', $scope)->first();

            // assign role to user
            $this->user->roles()->detach($role->id);
            $this->user->roles()->attach($role->id);

            // put scope to request
            $request['scope'] = $scope;

            call_user_func(
                LaravelMobilePassportSingleton::$otpConfirmCallBack,
                $request,
                $this->user
            );

            return $this->IssueToken($request);
        } else {
            // not Successful
            $response->setMessage($this->getTrans(__FUNCTION__, 'token_failed'));
            $response->setStatus(false);
            $response->setError(401);
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
                return $defaultUser['token'];
            }
        }

        if (is_null(Cache::get($tokenKey))) {
            // not any cached token

            $token = rand(1000, 9999);

            Cache::put($tokenKey, $token, $this->otpTokenExpireTime);
        } else {

            $token = Cache::get($tokenKey);
        }

        return $token;
    }

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
                $response->setStatus(false);
                $response->setError(99);
                return SmartResponse::response($response);
            }
        }

        $this->user = $this->firstOrCreateUser($request);

        if (is_null($this->user)) {
            $response->setMessage($this->getTrans(__FUNCTION__, 'email_failed'));
            $response->setStatus(false);
            $response->setError(401);
            return SmartResponse::response($response);
        }

        //check for user roles
        if (!$this->userHavePermission($request)) {
            $response->setMessage($this->getTrans(__FUNCTION__, 'permission_failed'));
            $response->setStatus(false);
            $response->setError(401);
            return SmartResponse::response($response);
        }


        $this->firstOrCreateDevice($request);

        $hash = app('hash');
        if ($hash->check($request['password'], $this->user->password)) {
            // Successful
            return $this->IssueToken($request);
        } else {
            // not Successful
            $response->setMessage($this->getTrans(__FUNCTION__, 'password_failed'));
            $response->setStatus(false);
            $response->setError(401);
            return SmartResponse::response($response);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function IssueToken(Request $request)
    {
        // response object
        $response = new ResponseModel();

        $token = $this->user->createToken($request['scope'], [$request['scope']]);

        $response->setData(collect([
            'user_id' => $token->toArray()['token']['user_id'],
            'scope' => $request['scope'],
            'accessToken' => $token->toArray()['accessToken'],
            'expires_at' => $token->toArray()['token']['expires_at'],
        ]));
        $response->setMessage($this->getTrans(__FUNCTION__, 'successful'));

        return SmartResponse::response($response);
    }


    /**
     * @param $method
     * @param $status
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function getTrans($method, $status)
    {
        return trans('laravel_smart_restful::' . 'mobile_passport.' . get_class($this) . '.' . $method . '.' . $status);
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
            $request['country_code'] .
            $request['phone_number'];
        return $key;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function userHavePermission(Request $request): bool
    {
        $userHaveRole = false;
        foreach ($this->user->toArray()['roles'] as $roleParams) {
            if ($roleParams['title'] == $request['scope']) {
                $userHaveRole = true;
            }
        }
        return $userHaveRole;
    }
}