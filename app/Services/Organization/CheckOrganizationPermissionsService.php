<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 06.08.18
 * Time: 11:37
 */

namespace App\Services\Organization;

use App\Contracts\CheckOrganizationPermissionsInterface;
use App\Models\Db\Organization;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Вся логика проверок круда и управления организациями тут
 *
 * Class CheckPermissionsService
 * @package App\Repositories\Organization
 */
class CheckOrganizationPermissionsService implements CheckOrganizationPermissionsInterface
{
    /**
     * Проверка, может ли юзер получить организацию
     *
     * @param Model $organization
     * @return void
     * @throws Exception
     */
    public function checkGetPermission(Model $organization): void
    {
        if (Auth::user()->cant('get', $organization)) {
            throw new Exception('User cant get record of this organization');
        }
    }

    /**
     * Проверка разрешений на создание
     *
     * @return void
     * @throws Exception
     */
    public function checkCreatePermission(): void
    {
        if (Auth::user()->cant('create', Organization::class)) {
            throw new Exception('User is not an owner');
        }
    }

    /**
     * Проверка доступов при обновлении
     *
     * @param Model $organization
     * @return void
     * @throws Exception
     */
    public function checkUpdatePermission(Model $organization): void
    {
        if (Auth::user()->cant('manageOrganization', $organization)) {
            throw new Exception('Update operation is unavailable');
        }
    }

    /**
     * Проверка прав на удаление
     *
     * @param Model $organization
     * @return void
     * @throws Exception
     */
    public function checkDeletePermission(Model $organization): void
    {
        if (Auth::user()->cant('manageOrganization', $organization)) {
            throw new Exception('Delete action is unavailable');
        }
    }

    /**
     * Проверка прав приглашения юзеров
     *
     * @param int $userId
     * @param int $organizationId
     * @return void
     * @throws Exception
     */
    public function checkInvitePermission(int $userId, int $organizationId): void
    {
        if (Auth::user()->cant('invite', [Organization::findOrFail($organizationId), $userId])) {
            throw new Exception("Unable to invite user");
        }
    }
}