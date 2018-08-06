<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 0:59
 */

namespace App\Api\V1\Http\Controllers\Task;

use Exception;
use App\Models\Db\Task;
use Dingo\Api\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Transformers\TaskTransformer;
use App\Contracts\RepositoryInterface;
use App\Repositories\Task\TaskRepository;
use App\Api\V1\Http\Controllers\Controller;
use App\Contracts\CheckTaskPermissionInterface;
use App\Api\V1\Http\Requests\Task\UpdateTaskRequest;
use App\Api\V1\Http\Requests\Task\CreateTaskRequest;
use App\Api\V1\Http\Requests\Task\TaskByPerformerRequest;
use App\Api\V1\Http\Requests\Task\SetTaskPerformerRequest;

/**
 * Class TaskController
 * @package App\Http\Controllers\Api\Task
 */
class TaskController extends Controller
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var CheckTaskPermissionInterface
     */
    private $permission;

    /**
     * TaskController constructor.
     * @param RepositoryInterface $taskRepository
     * @param CheckTaskPermissionInterface $permission
     */
    public function __construct(RepositoryInterface $taskRepository, CheckTaskPermissionInterface $permission)
    {
        $this->middleware('jwt.auth');

        $this->taskRepository = $taskRepository;
        $this->permission = $permission;
    }

    /**
     * @SWG\Get(
     *    path = "/api/task/get-all",
     *    tags={"Task"},
     *    description="Get list or Owner/Performer tasks",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="page",
     *       in="query",
     *       type="integer",
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Tasks")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *    definition="Tasks",
     *    type="object",
     *    @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/Task")),
     *    @SWG\Property(property="meta", type="object", ref="#/definitions/Pagination"))
     * ),
     * @SWG\Definition(
     *     definition="Task",
     *     type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="status", type="string"),
     *     @SWG\Property(property="deadline", type="string"),
     *     @SWG\Property(property="performer", ref="#/definitions/User"),
     *     @SWG\Property(property="author", ref="#/definitions/User"),
     *     @SWG\Property(property="organization", ref="#/definitions/Organization"))
     * )
     *
     * Получение списка тасков
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->response->paginator($this->taskRepository->getAll(10), new TaskTransformer());
    }

    /**
     * @SWG\Get(
     *    path = "/api/task/get/{id}",
     *    tags={"Task"},
     *    description="Get task by id",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="id",
     *       in="path",
     *       type="integer",
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Task")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 404,
     *       description = "Model Not Found",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "This user cant get the task",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * )
     *
     * Получение записи таска
     *
     * @param int $id
     * @return Response
     */
    public function get(int $id): Response
    {
        $task = Task::findOrFail($id);
        $this->permission->checkGetPermission($task);

        return $this->response->item($task, new TaskTransformer());
    }

    /**
     * @SWG\Post(
     *    path = "/api/task/create",
     *    tags={"Task"},
     *    description="Create task",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/CreateTaskRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Task")
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while creating an task",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "Create action is unavailable",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="CreateTaskRequest",
     *     type="object",
     *     @SWG\Property(property="performer_id", type="integer"),
     *     @SWG\Property(property="organization_id", type="integer"),
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="deadline", type="string")
     * )
     *
     * Создание таска
     *
     * @param CreateTaskRequest $request
     * @return Response
     */
    public function create(CreateTaskRequest $request): Response
    {
        $this->permission->checkCreatePermission($request->get('organization_id'), $request->get('performer_id'));
        if (!$this->taskRepository->create($request->validated())) {
            $this->response->error('Error while creating task', 400);
        }

        return $this->response->item($this->taskRepository->getModel(), new TaskTransformer());
    }

    /**
     * @SWG\Put(
     *    path = "/api/task/update/{id}",
     *    tags={"Task"},
     *    description="Update task",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="id",
     *       in="path",
     *       type="integer",
     *       required=true
     *    ),
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/UpdateTaskRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Task")
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while updating a task",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "Update operation is unavailable|No query results for model",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="UpdateTaskRequest",
     *     type="object",
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="deadline", type="string"),
     *     @SWG\Property(property="cancellation_reason", type="string"),
     *     @SWG\Property(property="rejection_reason", type="string")
     * )
     *
     * Обновление таска
     *
     * @param int $id
     * @param UpdateTaskRequest $request
     * @return Response
     */
    public function update(int $id, UpdateTaskRequest $request): Response
    {
        $task = Task::findOrFail($id);
        $this->permission->checkUpdatePermission($task);
        if (!$this->taskRepository->setModel($task)->update($request->validated())) {
            $this->response->error('Error while updating task', 400);
        }

        return $this->response->item($this->taskRepository->getModel(), new TaskTransformer());
    }

    /**
     * @SWG\Delete(
     *    path = "/api/task/delete/{id}",
     *    tags={"Task"},
     *    description="Delete task",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="id",
     *       in="path",
     *       type="integer",
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while deleting a task",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "Delete action is unavailable|No query results for model",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * Удаление таска
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $task = Task::findOrFail($id);
        $this->permission->checkDeletePermission($task);
        if (!$this->taskRepository->setModel($task)->delete()) {
            $this->response->error('Error while deleting task', 400);
        }

        return $this->response->noContent()->statusCode(200);
    }

    /**
     * @SWG\Get(
     *    path = "/api/task/statuses",
     *    tags={"Task"},
     *    description="Get task statuses",
     *    produces={"application/json"},
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Statuses")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="Statuses",
     *     type="object",
     *     @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/Status")),
     * ),
     * @SWG\Definition(
     *     definition="Status",
     *     type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="title", type="string"),
     * )
     *
     * Получение статусов для фронта
     *
     * @return JsonResponse
     */
    public function statuses(): JsonResponse
    {
        return response()->json(['data' => Task::$statusList]);
    }

    /**
     * @SWG\Put(
     *    path = "/api/task/change-status/{id}",
     *    tags={"Task"},
     *    description="Change status of a task",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="id",
     *       in="path",
     *       type="integer",
     *       required=true
     *    ),
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/TaskByPerformerRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while updating task status",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "Update operation is unavailable|No query results for model",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="TaskByPerformerRequest",
     *     type="object",
     *     @SWG\Property(property="status", type="integer"),
     * )
     *
     * Обновление задачи перформером
     *
     * @param int $id
     * @param TaskByPerformerRequest $request
     * @return Response
     * @throws Exception
     */
    public function changeStatus(int $id, TaskByPerformerRequest $request): Response
    {
        $task = Task::findOrFail($id);
        $this->permission->checkPerformerUpdatePermission($task);
        if (!$this->taskRepository->setModel($task)->update($request->validated())) {
            $this->response->error('Error while updating task status', 400);
        }

        return $this->response->item($this->taskRepository->getModel(), new TaskTransformer());
    }

    /**
     * @SWG\Put(
     *    path = "/api/task/set-performer/{id}",
     *    tags={"Task"},
     *    description="Set task performer",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="id",
     *       in="path",
     *       type="integer",
     *       required=true
     *    ),
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/SetTaskPerformerRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while setting performer for a task",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "Set Performer operation is unavailable|No query results for model",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="SetTaskPerformerRequest",
     *     type="object",
     *     @SWG\Property(property="performer_id", type="integer"),
     * )
     *
     * Назначение исполнителя на задачу
     *
     * @param int $id
     * @param SetTaskPerformerRequest $request
     * @return Response
     * @throws Exception
     */
    public function setPerformer(int $id, SetTaskPerformerRequest $request): Response
    {
        $task = Task::findOrFail($id);
        $this->permission->checkSetPerformerPermission($task, $request->get('performer_id'));
        if (!$this->taskRepository->setModel($task)->setPerformer($request->validated())) {
            $this->response->error('Error while setting performer for a task', 400);
        }

        return $this->response->noContent()->statusCode(200);
    }
}