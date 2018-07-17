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
                    Route::get('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@index');
                    Route::get('create', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@create');
                    Route::get('{id}/edit', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@edit');
                    Route::get('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@show');
                    Route::post('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@store');
                    Route::put('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@update');
                    Route::patch('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@update');
                    Route::delete('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportRoleController@destroy');
                });
                Route::prefix('device')->group(function () {
                    Route::get('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@index');
                    Route::get('create', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@create');
                    Route::get('{id}/edit', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@edit');
                    Route::get('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@show');
                    Route::post('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@store');
                    Route::put('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@update');
                    Route::patch('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@update');
                    Route::delete('{id}', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportDeviceController@destroy');
                });
                Route::prefix('auth')->group(function () {
                    Route::post('', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportAuthController@store');
                    Route::post('confirm', 'Alive2212\LaravelMobilePassport\Http\Controllers\MobilePassportAuthController@confirmOtp');
                });
            });
        });
    });
});

