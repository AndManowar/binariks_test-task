<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 06.08.18
 * Time: 12:21
 */

namespace App\Contracts;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CheckTaskPermissionInterface
 * @package App\Contracts
 */
interface CheckTaskPermissionInterface
{
    /**
     * Проверка прав на получение задачи
     *
     * @param Model $task
     * @return void
     */
    public function checkGetPermission(Model $task):void;

    /**
     * Проверка прав на создание
     *
     * @param int $organizationId
     * @param int $performerId
     * @return void
     */
    public function checkCreatePermission(int $organizationId, int $performerId): void;

    /**
     * Проверка прав при обновлении
     *
     * @param Model $task
     * @return void
     */
    public function checkUpdatePermission(Model $task): void;

    /**
     * Проверка прав на удаление
     *
     * @param Model $task
     * @return void
     */
    public function checkDeletePermission(Model $task): void;

    /**
     * Проверка прав на обновление исполнителем
     *
     * @param Model $task
     * @return void
     */
    public function checkPerformerUpdatePermission(Model $task):void;

    /**
     * Проверка прав на назначение исполнителя
     *
     * @param Model $task
     * @param int $performerId
     * @return void
     */
    public function checkSetPerformerPermission(Model $task, int $performerId):void;
}