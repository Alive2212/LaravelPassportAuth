# LaravelMobilePassport

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require alive2212/laravelmobilepassport
```
if use Laravel < 5.4 add to service provider 'config/app.php'
```php
    'providers' => [
        ...
        
        /*
         * Authentication service
         */
        Alive2212\LaravelMobilePassport\LaravelMobilePassportServiceProvider::class,
    ],
```

migrate all database
```
$ php artisan migrate
```
add following code into AuthServiceProvider in 'boot' method
```php
public function boot()
{
    $this->registerPolicies();

    Passport::routes();

    LaravelMobilePassport::initPassportTokenCan();

    LaravelMobilePassportSingleton::$otpCallBack = function (
        Request $reqeust,
        User $user,
        AliveMobilePassportDevice $device,
        $token
    ) {
        // dispach send sms job here to send notification
    };
    
    
    LaravelMobilePassportSingleton::$otpConfirmCallBack = function (
        Request $reqeust,
        User $user
    ) {
        // put somthing here like update user name with request fileds
    };
    
}
```
*tip: if ENV_DEBUG in .env file set to false don't return any data in register by token 

add 'phone_number' & 'country_code' into model $fillable variable:
```php
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'country_code',
    ];
```

publish vendor files with following command:
```
php artisan vendor:publish --tag laravel_mobile_passport.lang
```

add to user model
```php
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            AliveMobilePassportRole::class,
            'alive_mobile_passport_role_user',
            'user_id',
            'role_id'
        );
    }
```

## Usage



## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/alive2212/laravelmobilepassport.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/alive2212/laravelmobilepassport.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/alive2212/laravelmobilepassport/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/alive2212/laravelmobilepassport
[link-downloads]: https://packagist.org/packages/alive2212/laravelmobilepassport
[link-travis]: https://travis-ci.org/alive2212/laravelmobilepassport
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/alive2212
[link-contributors]: ../../contributors]