<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 06.08.18
 * Time: 11:42
 */

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;


/**
 * Интерфейс для осуществления проверок связанных с организациями
 *
 * Class CheckOrganizationPermissionsInterface
 * @package App\Contracts
 */
interface CheckOrganizationPermissionsInterface
{
    /**
     * Проверка возможности получения модели
     *
     * @param Model $organization
     * @return void
     */
    public function checkGetPermission(Model $organization): void;

    /**
     * Проверка разрешений на создание
     *
     * @return void
     */
    public function checkCreatePermission(): void;

    /**
     * Проверка доступов при обновлении
     *
     * @param Model $organization
     * @return void
     */
    public function checkUpdatePermission(Model $organization): void;

    /**
     * Проверка прав на удаление
     *
     * @param Model $organization
     * @return void
     */
    public function checkDeletePermission(Model $organization): void;

    /**
     * Проверка прав приглашения юзеров
     *
     * @param int $userId
     * @param int $organizationId
     * @return void
     */
    public function checkInvitePermission(int $userId, int $organizationId): void;
}