<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 03.08.2018
 * Time: 21:39
 */

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\FormRequest;

/**
 * Class LoginRequest
 * @package App\Http\Requests
 */
class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }
}