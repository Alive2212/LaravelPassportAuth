<?php

namespace Alive2212\LaravelMobilePassport\Http\Controllers;

use Alive2212\LaravelMobilePassport\AliveMobilePassportRole;
use Alive2212\LaravelSmartRestful\BaseController;

class MobilePassportRoleController extends BaseController
{
    public function initController()
    {
        $this->model = new AliveMobilePassportRole();
        $this->middleware([
//            'auth:api',
        ]);
    }
}