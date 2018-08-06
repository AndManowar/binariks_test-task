<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 14:12
 */

namespace App\Transformers;

use App\Models\Db\Task;
use League\Fractal\TransformerAbstract;

/**
 * Трансформер даты тасков
 *
 * Class TaskTransformer
 * @package App\Transformers
 */
class TaskTransformer extends TransformerAbstract
{
    /**
     * @param Task $task
     * @return array
     */
    public function transform(Task $task): array
    {
        return [
            'id'           => $task->id,
            'name'         => $task->name,
            'status'       => $task->getCurrentStatus(),
            'deadline'     => $task->deadline,
            'performer'    => !is_null($task->performer_id) ? (new UserTransformer())->transform($task->performer) : null,
            'author'       => (new UserTransformer())->transform($task->author),
            'organization' => (new OrganizationTransformer())->transform($task->organization),
        ];
    }
}