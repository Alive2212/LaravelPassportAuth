<?php

namespace Alive2212\LaravelMobilePassport\Http\Controllers;

use Alive2212\LaravelMobilePassport\Models\AliveMobilePassportDevice;
use Alive2212\LaravelSmartRestful\BaseController;

class MobilePassportDeviceController extends BaseController
{
    public function initController()
    {
        $this->model = new AliveMobilePassportDevice();
        $this->middleware([
        ]);
    }
}