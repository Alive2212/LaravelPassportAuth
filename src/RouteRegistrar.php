<?php

namespace Alive2212\LaravelMobilePassport;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forRestfulRoute();
        $this->forCustomRoute();
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function forRestfulRoute()
    {
        $this->router->group([
            'prefix' => config('laravel-mobile-passport.route.restful_prefix'),
        ], function (Router $router) {
            $router->resource('/passport/role', 'MobilePassportRoleController');
            $router->resource('/passport/device', 'MobilePassportDeviceController');
        });
    }

    /**
     *
     */
    public function forCustomRoute()
    {
        $this->router->group([
            'prefix' => config('laravel-mobile-passport.route.custom_prefix'),
        ], function (Router $router) {
            $router->post(
                '/passport/auth/register',
                'MobilePassportAuthController@store'
            )->name('mobile_passport.auth.register');
            $router->post(
                '/passport/auth/confirm',
                'MobilePassportAuthController@confirmOtp'
            )->name('mobile_passport.auth.confirm');
        });
    }
}
