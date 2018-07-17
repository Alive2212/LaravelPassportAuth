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

Route::prefix('api')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('alive')->group(function () {
            Route::prefix('passport')->group(function () {
                Route::prefix('role')->group(function () {
                    Route::get('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@index')->name('mobile_passport.role.index');
                    Route::get('create', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@create')->name('mobile_passport.role.create');
                    Route::get('{id}/edit', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@edit')->name('mobile_passport.role.edit');
                    Route::get('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@show')->name('mobile_passport.role.show');
                    Route::post('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@store')->name('mobile_passport.role.store');
                    Route::put('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@update')->name('mobile_passport.role.put');
                    Route::patch('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@update')->name('mobile_passport.role.patch');
                    Route::delete('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@destroy')->name('mobile_passport.role.destroy');
                });
                Route::prefix('device')->group(function () {
                    Route::get('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@index')->name('mobile_passport.device.index');
                    Route::get('create', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@create')->name('mobile_passport.device.create');
                    Route::get('{id}/edit', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@edit')->name('mobile_passport.device.edit');
                    Route::get('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@show')->name('mobile_passport.device.show');
                    Route::post('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@store')->name('mobile_passport.device.store');
                    Route::put('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@update')->name('mobile_passport.device.put');
                    Route::patch('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@update')->name('mobile_passport.device.patch');
                    Route::delete('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@destroy')->name('mobile_passport.device.destroy');
                });
                Route::prefix('auth')->group(function () {
                    Route::post('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportAuthController@store')->name('mobile_passport.auth.register');
                    Route::post('confirm', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportAuthController@confirmOtp')->name('mobile_passport.auth.confirm');
                });
            });
        });
    });
});

