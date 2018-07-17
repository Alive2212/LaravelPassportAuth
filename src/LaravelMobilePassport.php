<?php

namespace Alive2212\LaravelMobilePassport;

use Laravel\Passport\Passport;

class LaravelMobilePassport
{
    public static function initPassportTokenCan()
    {
        $roles = new AliveMobilePassportRole();
        $scopes = [];
        foreach ($roles->get()->toArray() as $role) {
            $scopes = array_add($scopes, $role['title'], $role['description']);
        }
        Passport::tokensCan($scopes);
    }
}