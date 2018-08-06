<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 1:26
 */

namespace App\Api\V1\Http\Requests\Task;

use App\Api\V1\Http\Requests\FormRequest;

/**
 * Class TaskRequest
 * @package App\Http\Requests\Task
 */
class CreateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'performer_id'        => 'nullable|integer|exists:users,id',
            'organization_id'     => 'required|integer|exists:organizations,id',
            'name'                => 'required|max:100',
            'deadline'            => 'required|date_format:Y-m-d|after:yesterday',
        ];
    }
}