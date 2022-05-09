<?php

namespace Alive2212\LaravelMobilePassport\Http\Controllers;

use Alive2212\LaravelMobilePassport\Models\AliveMobilePassportRole;
use Alive2212\LaravelSmartRestful\BaseController;

class MobilePassportRoleController extends BaseController
{
    /**
     * @var array
     */
    protected $pivotFields = [
        'authors',
        'groups',
    ];

    public function initController()
    {
        $this->model = new AliveMobilePassportRole();
        $this->middleware([
        ]);
    }
}