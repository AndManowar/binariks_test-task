<?php

namespace App\Events;

use App\Models\Db\Organization;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Событие создания организации
 *
 * Class OnOrganizationCreate
 * @package App\Events
 */
class OnOrganizationCreate
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Organization
     */
    public $organization;

    /**
     * Create a new event instance.
     * @param Organization $organization
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }
}
