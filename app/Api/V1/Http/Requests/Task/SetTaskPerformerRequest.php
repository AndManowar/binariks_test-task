<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 11:55
 */

namespace App\Api\V1\Http\Requests\Task;

use App\Api\V1\Http\Requests\FormRequest;


/**
 * Реквестим для назначения исполнителя на задачу
 *
 * Class SetTaskPerformerRequest
 * @package App\Http\Requests\Task
 */
class SetTaskPerformerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'performer_id' => 'required|integer|exists:users,id',
        ];
    }
}