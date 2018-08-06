<?php

namespace App\Listeners;

use App\Events\OnUserRegister;
use App\Traits\EventLoggerTrait;

/**
 * Обработчик события регистрации пользователя
 *
 * Class UserRegistrationListener
 * @package App\Listeners
 */
class UserRegistrationListener
{
    use EventLoggerTrait;

    /**
     * @const
     */
    const MESSAGE_FROM_USER_REGISTRATION_LISTENER = 'Message from UserRegistrationListener - ';

    /**
     * Handle the event.
     *
     * @param  OnUserRegister $event
     * @return void
     */
    public function handle(OnUserRegister $event)
    {
        $this->writeToLog(self::MESSAGE_FROM_USER_REGISTRATION_LISTENER.$event->user->getFullName());
    }
}
