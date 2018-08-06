<?php

namespace App\Listeners;

use App\Events\OnSetTaskPerformer;
use App\Traits\EventLoggerTrait;

/**
 * Слушатель события изменения исполнителя задачи
 *
 * Class SetTaskPerformerListener
 * @package App\Listeners
 */
class SetTaskPerformerListener
{
    use EventLoggerTrait;

    /**
     * @const
     */
    const MESSAGE_FROM_SET_TASK_PERFORMER_LISTENER = 'Message from SetTaskPerformerListener - ';

    /**
     * Handle the event.
     *
     * @param  OnSetTaskPerformer $event
     * @return void
     */
    public function handle(OnSetTaskPerformer $event)
    {
        $message = 'Task:'.$event->task->name.
            ';Organization:'.$event->task->organization->organization_name.
            ';Created By:'.$event->task->author->getFullName().
            ';Performer:'.$event->task->performer->getFullName();

        $this->writeToLog(self::MESSAGE_FROM_SET_TASK_PERFORMER_LISTENER.$message);
    }
}
