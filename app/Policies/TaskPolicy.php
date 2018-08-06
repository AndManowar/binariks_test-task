<?php

namespace App\Policies;

use App\Models\Db\Organization;
use App\Models\Db\Task;
use App\Models\Db\User;
use App\Models\Db\UserOrganization;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TaskPolicy
 * @package App\Policies
 */
class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Может ли юзер проводить определенные действия с задачей
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function manage(User $user, Task $task): bool
    {
        return $user->role === User::ROLE_OWNER && $task->author_id === $user->id;
    }

    /**
     * Проверка прав на создание задачи
     *
     * @param User $user
     * @param Organization $organization
     * @param int $performerId
     * @return bool
     */
    public function create(User $user, Organization $organization, int $performerId = null): bool
    {
        if (is_null($performerId)) {
            return $user->can('manageOrganization', $organization);
        }

        return $user->can('manageOrganization', $organization) && $this->isPerformerPresentInOrganisation($performerId, $organization->id);
    }

    /**
     * Можно ли назначить исполнителя задачи
     *
     * @param User $user
     * @param Task $task
     * @param int $performerId
     * @return bool
     */
    public function setPerformer(User $user, Task $task, int $performerId): bool
    {
        return $this->manage($user, $task) && !$this->isPerformerAlreadyPerformsTheTask($task, $performerId);
    }

    /**
     * Может ли юзер получить эту задачу
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function get(User $user, Task $task): bool
    {
        return $user->role === User::ROLE_OWNER
            ? $task->author_id === $user->id
            : $task->performer_id === $user->id;
    }

    /**
     * Проверочка, может ли перформер обновлять задачу
     *
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function updateByPerformer(User $user, Task $task): bool
    {
        return $user->role === User::ROLE_PERFORMER && $task->performer_id === $user->id;
    }

    /**
     * Проверочка, может ли овнер создавать задачу на исполнителя (присутствует ли он в его организации)
     * и является ли исполнителем вообще
     *
     * @param int $performerId
     * @param int $organizationId
     * @return bool
     */
    private function isPerformerPresentInOrganisation(int $performerId, int $organizationId): bool
    {
        /** @var User $performer */
        $performer = User::findOrFail($performerId);
        // Проверяем исполнителя на роль и состоит ли он в присланной в реквесте организации
        return $performer->role === User::ROLE_PERFORMER && UserOrganization::query()
                ->join('organizations', 'organization_id', '=', 'organizations.id')
                ->where([
                    ['user_id', '=', $performerId],
                    ['owner_id', '=', Auth::id()],
                    ['organization_id', '=', $organizationId],
                ])
                ->exists();
    }

    /**
     * Не исполняет ли уже назначаемый юзер эту задачу
     *
     * @param Task $task
     * @param int $performerId
     * @return bool
     */
    private function isPerformerAlreadyPerformsTheTask(Task $task, int $performerId): bool
    {
        return $task->performer_id === $performerId;
    }
}
