<?php

namespace Alive2212\LaravelMobilePassport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateThirdPartyUserToken extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id' => 'required',
            'client_secret' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'password' => 'required',
            'name' => 'required',
            'imei' => 'required',
            'app_name' => 'required',
            'app_version' => 'required',
            'platform' => 'required',
            'os' => 'required',
            'push_token' => 'required',

        ];
    }
}
