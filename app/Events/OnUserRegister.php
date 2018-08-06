<?php

namespace App\Events;

use App\Models\Db\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Событие регистрации юзера
 *
 * Class OnUserRegister
 * @package App\Events
 */
class OnUserRegister
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new event instance.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
