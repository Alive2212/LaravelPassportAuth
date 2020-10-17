# LaravelMobilePassport

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require alive2212/laravel-mobile-passport
```
add to service provider 'config/app.php'
```php
    'providers' => [
        ...
        
        /*
         * Authentication service
         */
        Alive2212\LaravelMobilePassport\LaravelMobilePassportServiceProvider::class,
    ],
```

if use Lumen add following service provider at then end of 'bootstrap/app.php' 
```php
$app->register(Alive2212\LaravelMobilePassport\LaravelMobilePassportServiceProvider::class);

$app->router->group([
    'namespace' => 'Alive2212\LaravelMobilePassport\Http\Controllers',
], function ($router) {
    require Alive2212\LaravelMobilePassport\LaravelMobilePassport::getDir() .
        '/lumen_routes.php';
});
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
        Request $request,
        User $user,
        AliveMobilePassportDevice $device,
        $token
    ) {
        // dispatch send sms job here to send notification

    };
    
    
    LaravelMobilePassportSingleton::$otpConfirmCallBack = function (
        Request $request,
        User $user,
        PersonalAccessTokenResult $token,
        ResponseModel $response
    ) {
        // put something here like update user name with request fields
        
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
php artisan vendor:publish --tag laravel-mobile-passport.lang
php artisan vendor:publish --tag laravel-mobile-passport.config
```
User model must be extended from BaseAuthModel

In the next step you should install following package and it's version related to your laravel version.
* phoenix/eloquent-meta
* fico7489/laravel-pivot

Add flowing code into `app\User.php` 

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

Finally run following command to install passport dependency
```php
$ art passport:install
```

Optional
for add routes you can put following into boot method at AppServiceProvider 
```php
// add mobile passport routes
LaravelMobilePassport::routes(null,['middleware'=>'cors']);
```

## Usage
1- create roles what you want in to alive_mobile_passport_roles
*tip: title of roles must unique

You can use following to get current user scopes
```php
$request['access_token'])
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

Relation of this package with another one is:
* array helper -> nothing
* laravel excel helper -> nothing
* laravel onion pattern -> nothing
* laravel query helper -> nothing
* laravel reflection helper -> nothing
* laravel request helper -> nothing
* laravel string helper -> nothing
* laravel smart response -> array helper
* laravel smart restful -> laravel excel helper, laravel onion pattern, laravel query helper, laravel reflection helper, laravel smart response, laravel string helper
* laravel passport auth -> laravel smart restful 

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