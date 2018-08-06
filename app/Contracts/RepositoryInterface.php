<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 04.08.2018
 * Time: 1:14
 */

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Общий интерфейс для всех репозиториев
 *
 * Interface RepositoryInterface
 * @package App\Repositories
 */
interface RepositoryInterface
{
    /**
     * Присвоение модели через fluent
     *
     * @param Model $model
     * @return RepositoryInterface
     */
    public function setModel(Model $model):RepositoryInterface;

    /**
     * Получение пагинированного списка моделей
     *
     * @param int $pageSize
     * @return LengthAwarePaginator
     */
    public function getAll(int $pageSize = 5): LengthAwarePaginator;

    /**
     * Создать запись
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool;

    /**
     * Обновить запись
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool;

    /**
     * Удалить запись
     *
     * @return bool
     */
    public function delete(): bool;

    /**
     * Получить модель
     *
     * @return Model
     */
    public function getModel(): Model;
}