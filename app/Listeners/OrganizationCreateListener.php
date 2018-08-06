<?php

namespace App\Listeners;

use App\Events\OnOrganizationCreate;
use App\Traits\EventLoggerTrait;

/**
 * Слушатель события создания организации
 *
 * Class OrganizationCreateListener
 * @package App\Listeners
 */
class OrganizationCreateListener
{
    use EventLoggerTrait;

    /**
     * @const
     */
    const MESSAGE_FROM_ORGANIZATION_EVENT_LISTENER = 'Message from OrganizationCreateListener - ';

    /**
     * Handle the event.
     *
     * @param  OnOrganizationCreate $event
     * @return void
     */
    public function handle(OnOrganizationCreate $event)
    {
        $this->writeToLog(self::MESSAGE_FROM_ORGANIZATION_EVENT_LISTENER.$event->organization->organization_name);
    }
}
