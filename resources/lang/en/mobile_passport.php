<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Mobile Passport Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    'Alive2212\LaravelMobilePassport\Http\Controllers\AliveParsianPaymentController' => [
        'store' => [
            'validation_failed' => 'Validation Field',
            'scope_exist_failed' => 'Can\'t find this scope',
            'scope_field_failed' => 'Can\'t find this scope',
        ],
        'registerByPassword' => [
            'validation_failed' => 'Validation Field',
            'email_failed' => 'User or password was wrong',
            'permission_failed' => 'permission denied. you have\'t this role.',
            'password_failed' => 'User or password was wrong',
        ],

        'registerByOtp' => [
            'validation_failed' => 'Validation Field',
            'successful' => 'Token was created successfully',
        ],

        'confirmOtp' => [
            'validation_failed' => 'Validation Field',
            'token_failed' => 'Token Field',
        ],

        'IssueToken' => [
            'successful' => 'Token was created successfully',
        ],
    ],
];
