<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 2:01
 */

namespace App\Api\V1\Http\Requests\Task;

use App\Api\V1\Http\Requests\FormRequest;
use App\Models\Db\Task;

/**
 * Реквест от исполнителя (может менять только статус)
 *
 * Class TaskByPerformerRequest
 * @package App\Http\Requests\Task
 */
class TaskByPerformerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => 'required|integer|in:'.Task::STATUS_NEW.','.Task::STATUS_IN_PROGRESS.','.Task::STATUS_REJECTED.','.Task::STATUS_CANCELLED.','.Task::STATUS_DONE,
        ];
    }
}