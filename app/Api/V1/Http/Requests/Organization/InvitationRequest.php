<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 0:49
 */

namespace App\Api\V1\Http\Requests\Organization;

use App\Api\V1\Http\Requests\FormRequest;


/**
 * Реквест пришлашения(добавления) в организацию
 *
 * Class InvitationRequest
 * @package App\Http\Requests\Organization
 */
class InvitationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id'         => 'required|integer|exists:users,id',
            'organization_id' => 'required|integer|exists:organizations,id',
        ];
    }
}