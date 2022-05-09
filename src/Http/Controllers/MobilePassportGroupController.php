<?php

namespace Alive2212\LaravelMobilePassport\Http\Controllers;

use Alive2212\LaravelMobilePassport\Models\AliveMobilePassportGroup;
use Alive2212\LaravelSmartRestful\BaseController;

class MobilePassportGroupController extends BaseController
{
    /**
     * @var array
     */
    protected $pivotFields = [
        'authors',
        'roles',
    ];

    public function initController()
    {
        $this->model = new AliveMobilePassportGroup();
        $this->middleware([
        ]);
    }
}