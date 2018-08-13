<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'v1'], function () use ($router) {
        $router->group(['prefix' => 'alive'], function () use ($router) {
            $router->group(['prefix' => 'role'], function () use ($router) {
                $router->get('', [
                    'as' => 'mobile_passport.role.index',
                    'uses' => 'MobilePassportRoleController@index'
                ]);
                $router->get('create', [
                    'as' => 'mobile_passport.role.create',
                    'uses' => 'MobilePassportRoleController@create'
                ]);
                $router->get('{id}/edit', [
                    'as' => 'mobile_passport.role.edit',
                    'uses' => 'MobilePassportRoleController@edit',
                ]);
                $router->get('{id}', [
                    'as' => 'mobile_passport.role.show',
                    'uses' => 'MobilePassportRoleController@show',
                ]);
                $router->post('', [
                    'as' => 'mobile_passport.role.store',
                    'uses' => 'MobilePassportRoleController@store',
                ]);
                $router->put('{id}', [
                    'as' => 'mobile_passport.role.put',
                    'uses' => 'MobilePassportRoleController@update',
                ]);
                $router->patch('{id}', [
                    'as' => 'mobile_passport.role.patch',
                    'uses' => 'MobilePassportRoleController@update',
                ]);
                $router->delete('{id}', [
                    'as' => 'mobile_passport.role.destroy',
                    'uses' => 'MobilePassportRoleController@destroy',
                ]);
            });
            $router->group(['prefix' => 'device'], function () use ($router) {
                $router->get('', [
                    'as' => 'mobile_passport.device.index',
                    'uses' => 'MobilePassportDeviceController@index'
                ]);
                $router->get('create', [
                    'as' => 'mobile_passport.device.create',
                    'uses' => 'MobilePassportDeviceController@create'
                ]);
                $router->get('{id}/edit', [
                    'as' => 'mobile_passport.device.edit',
                    'uses' => 'MobilePassportDeviceController@edit',
                ]);
                $router->get('{id}', [
                    'as' => 'mobile_passport.device.show',
                    'uses' => 'MobilePassportDeviceController@show',
                ]);
                $router->post('', [
                    'as' => 'mobile_passport.device.store',
                    'uses' => 'MobilePassportDeviceController@store',
                ]);
                $router->put('{id}', [
                    'as' => 'mobile_passport.device.put',
                    'uses' => 'MobilePassportDeviceController@update',
                ]);
                $router->patch('{id}', [
                    'as' => 'mobile_passport.device.patch',
                    'uses' => 'MobilePassportDeviceController@update',
                ]);
                $router->delete('{id}', [
                    'as' => 'mobile_passport.device.destroy',
                    'uses' => 'MobilePassportDeviceController@destroy',
                ]);
            });
        });
        $router->group(['prefix' => 'custom'], function () use ($router) {
            $router->group(['prefix' => 'alive'], function () use ($router) {
                $router->group(['prefix' => 'passport'], function () use ($router) {
                    $router->group(['prefix' => 'auth'], function () use ($router) {
                        $router->post('register', [
                            'as' => 'mobile_passport.custom.auth.register',
                            'uses' => 'MobilePassportAuthController@store',
                        ]);
                        $router->post('confirm', [
                            'as' => 'mobile_passport.custom.auth.confirm',
                            'uses' => 'MobilePassportAuthController@confirmOtp',
                        ]);
                    });
                });
            });
        });

    });
});