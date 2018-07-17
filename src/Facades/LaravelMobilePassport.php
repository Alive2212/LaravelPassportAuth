<?php

namespace Alive2212\LaravelMobilePassport\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelMobilePassport extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelmobilepassport';
    }
}
