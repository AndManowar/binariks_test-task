<?php

namespace App\Events;

use App\Models\Db\Task;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Событие изменения исполнителя задачи
 *
 * Class OnSetTaskPerformer
 * @package App\Events
 */
class OnSetTaskPerformer
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Task
     */
    public $task;

    /**
     * Create a new event instance.
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}
