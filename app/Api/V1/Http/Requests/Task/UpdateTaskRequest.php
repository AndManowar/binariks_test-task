<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 13:44
 */

namespace App\Api\V1\Http\Requests\Task;

use App\Api\V1\Http\Requests\FormRequest;

/**
 * Class UpdateTaskRequest
 * @package App\Http\Requests\Task
 */
class UpdateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'                => 'required|max:100',
            'deadline'            => 'required|date_format:Y-m-d|after:yesterday',
            'cancellation_reason' => 'nullable|max:50',
            'rejection_reason'    => 'nullable|max:50',
        ];
    }
}