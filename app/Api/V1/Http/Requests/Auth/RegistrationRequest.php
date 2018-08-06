<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 03.08.2018
 * Time: 19:54
 */

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\FormRequest;
use App\Models\Db\User;

/**
 * Class RegistrationRequest
 * @package App\Requests
 */
class RegistrationRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'             => 'required|max:30',
            'surname'          => 'required|max:30',
            'email'            => 'required|email|unique:users',
            'password'         => 'required',
            'password_confirm' => 'required|same:password',
            'role'             => 'required|in:'.User::ROLE_OWNER.','.User::ROLE_PERFORMER,
        ];
    }
}