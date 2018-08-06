<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 06.08.18
 * Time: 12:21
 */

namespace App\Services\Task;

use App\Models\Db\Organization;
use App\Models\Db\Task;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\CheckTaskPermissionInterface;

/**
 * Сервис для проверки прав манипуляций задачами
 *
 * Class CheckTaskPermissionsService
 * @package App\Services
 */
class CheckTaskPermissionsService implements CheckTaskPermissionInterface
{

    /**
     * Проверка прав на получение задачи
     *
     * @param Model $task
     * @return void
     * @throws Exception
     */
    public function checkGetPermission(Model $task): void
    {
        if (Auth::user()->cant('get', $task)) {
            throw new Exception("This user cant get the task");
        }
    }

    /**
     * Проверка прав на создание
     *
     * @param int $organizationId
     * @param int|null $performerId
     * @return void
     * @throws Exception
     */
    public function checkCreatePermission(int $organizationId, int $performerId = null): void
    {
        if (Auth::user()->cant('create', [Task::class, Organization::findOrFail($organizationId), $performerId])) {
            throw new Exception('Create action is unavailable');
        }
    }

    /**
     * Проверка прав при обновлении
     *
     * @param Model $task
     * @return void
     * @throws Exception
     */
    public function checkUpdatePermission(Model $task): void
    {
        if (Auth::user()->cant('manage', $task)) {
            throw new Exception('Update operation is unavailable');
        }
    }

    /**
     * Проверка прав на удаление
     *
     * @param Model $task
     * @return void
     * @throws Exception
     */
    public function checkDeletePermission(Model $task): void
    {
        if (Auth::user()->cant('manage', $task)) {
            throw new Exception('Delete action is unavailable');
        }

    }

    /**
     * Проверка прав на обновление исполнителем
     *
     * @param Model $task
     * @return void
     * @throws Exception
     */
    public function checkPerformerUpdatePermission(Model $task): void
    {
        if (Auth::user()->cant('updateByPerformer', $task)) {
            throw new Exception('Update operation is unavailable');
        }
    }

    /**
     * Проверка прав на назначение исполнителя
     *
     * @param Model $task
     * @param int $performerId
     * @return void
     * @throws Exception
     */
    public function checkSetPerformerPermission(Model $task, int $performerId): void
    {
        if (Auth::user()->cant('setPerformer', [$task, $performerId])) {
            throw new Exception('Set performer operation is unavailable');
        }
    }
}