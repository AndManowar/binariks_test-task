<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 1:06
 */

namespace App\Repositories\Task;

use Auth;
use Exception;
use App\Models\Db\Task;
use App\Models\Db\User;
use App\Events\OnSetTaskPerformer;
use Illuminate\Support\Facades\Event;
use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * CRUD логика задач
 *
 * Class TaskRepository
 * @package App\Repositories\Task
 */
class TaskRepository implements RepositoryInterface
{
    /**
     * @var Task
     */
    protected $task;

    /**
     * Присвоение модели через fluent
     *
     * @param Model $model
     * @return RepositoryInterface|TaskRepository
     */
    public function setModel(Model $model): RepositoryInterface
    {
        $this->task = $model;

        return $this;
    }

    /**
     * Получение пагинированного списка моделей
     *
     * @param int $pageSize
     * @return LengthAwarePaginator
     */
    public function getAll(int $pageSize = 5): LengthAwarePaginator
    {
        return Task::query()
            ->where(Auth::user()->role === User::ROLE_OWNER ? 'author_id' : 'performer_id', '=', Auth::id())
            ->paginate($pageSize);
    }

    /**
     * Создать запись
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $this->task = new Task(array_merge($data, ['author_id' => Auth::id()]));

        if (!$this->task->save()) {
            return false;
        }

        // Если сразу создали на исполнителя - запускаем ивент
        $this->fireEventIfThePerformerHasChanged($data);

        return true;
    }

    /**
     * Обновить запись
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        return $this->task->fill($data)->save();
    }

    /**
     * Назначение исполнителя задачи
     *
     * @param array $data
     * @return bool
     */
    public function setPerformer(array $data): bool
    {
        $oldPerformerId = $this->task->performer_id;
        // Смело делаем fill, ничего кроме перформера не обновится, спасибо $request->validated()
        if (!$this->task->fill($data)->save()) {
            return false;
        }

        // Проверяем изменился ли исполнитель - если да - запускаем ивент
        $this->fireEventIfThePerformerHasChanged($data, $oldPerformerId);

        return true;
    }

    /**
     * Удалить запись
     *
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        return $this->task->delete();
    }

    /**
     * Получить модель таска
     *
     * @return Task|Model
     */
    public function getModel(): Model
    {
        // Заново дергаем поиск модели,
        // потому что статус ставится дефолтно,
        // и чтобы получить его - нужно сделать поиск модели
        return Task::findOrFail($this->task->id);
    }

    /**
     * Запускаем ивент изменения исполнителя если исполнитель есть в аттрибутах и он изменился
     *
     * @param array $data
     * @param int|null $oldPerformerId
     * @return void
     */
    private function fireEventIfThePerformerHasChanged(array $data, int $oldPerformerId = null): void
    {
        // Если пришел перформер и его новое значение не равно старому
        if (isset($data['performer_id']) && $oldPerformerId != $data['performer_id']) {
            Event::fire(new OnSetTaskPerformer($this->task));
        }
    }
}