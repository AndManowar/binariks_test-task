<?php

namespace App\Policies;

use App\Models\Db\User;
use App\Models\Db\Organization;
use App\Models\Db\UserOrganization;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class OrganizationPolicy
 * @package App\Policies
 */
class OrganizationPolicy
{
    use HandlesAuthorization;

    /**
     * Является ли юзер овнером, чтобы создавать организации
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role === User::ROLE_OWNER;
    }

    /**
     * Может ли юзер апдейтить/удалять и т д организацию
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function manageOrganization(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id && $user->role === User::ROLE_OWNER;
    }

    /**
     * Может ли юзер получить запись организации
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function get(User $user, Organization $organization): bool
    {
        if ($user->role === User::ROLE_PERFORMER) {
            return UserOrganization::query()
                ->where([['user_id', '=', $user->id], ['organization_id', '=', $organization->id]])
                ->exists();
        }

        return $organization->owner_id === $user->id;
    }

    /**
     * Можно ли пригласить юзера в оргинизацию
     *
     * @param User $user
     * @param Organization $organization
     * @param int $userId
     * @return bool
     */
    public function invite(User $user, Organization $organization, int $userId): bool
    {
        return $this->manageOrganization($user, $organization)
            && $this->checkExecutorRole($userId)
            && !$this->isUserAlreadyInvited($userId, $organization->id);

    }

    /**
     * Проверка юзера на роль исполнителя
     *
     * @param int $userId
     * @return bool
     */
    private function checkExecutorRole(int $userId): bool
    {
        /** @var User $user */
        $user = User::findOrFail($userId);
        return $user->role === User::ROLE_PERFORMER;
    }

    /**
     * Проверка, не состоит ли уже юзер в организации
     *
     * @param int $userId
     * @param int $organizationId
     * @return bool
     */
    private function isUserAlreadyInvited(int $userId, int $organizationId): bool
    {
        return UserOrganization::query()
            ->where([['user_id', '=', $userId], ['organization_id', '=', $organizationId]])
            ->exists();
    }
}
