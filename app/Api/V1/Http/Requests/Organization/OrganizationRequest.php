<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 03.08.2018
 * Time: 23:29
 */

namespace App\Api\V1\Http\Requests\Organization;

use App\Api\V1\Http\Requests\FormRequest;

/**
 * Реквест создания организации
 *
 * Class CreateOrganizationRequest
 * @package App\Http\Requests\Organization
 */
class OrganizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'organization_name' => 'required|max:50|unique:organizations',
            'registration_date' => 'required|date_format:Y-m-d',
        ];
    }
}