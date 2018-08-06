<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 03.08.2018
 * Time: 22:58
 */

namespace App\Repositories\Organization;

use Auth;
use Exception;
use App\Models\Db\User;
use App\Models\Db\Organization;
use App\Models\Db\UserOrganization;
use App\Events\OnOrganizationCreate;
use Illuminate\Support\Facades\Event;
use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class OrganizationRepository
 * @package App\Repositories\Organization
 */
class OrganizationRepository implements RepositoryInterface
{
    /**
     * @var Organization
     */
    protected $organization;

    /**
     * Присвоение модели через fluent
     *
     * @param Model $model
     * @return RepositoryInterface|OrganizationRepository
     */
    public function setModel(Model $model): RepositoryInterface
    {
        $this->organization = $model;

        return $this;
    }

    /**
     * Получение пагинированного списка моделей
     *
     * @param int $pageSize
     * @return LengthAwarePaginator
     */
    public function getAll(int $pageSize = 10): LengthAwarePaginator
    {
        $query = Organization::query();

        if (Auth::user()->role === User::ROLE_OWNER) {
            $query->where('owner_id', '=', Auth::id());
        } else {
            $query
                ->join('users_organizations', 'organization_id', '=', 'organizations.id')
                ->where('user_id', '=', Auth::id());
        }

        return $query->paginate($pageSize);
    }

    /**
     * Создать запись
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $this->organization = new Organization(array_merge($data, ['owner_id' => Auth::id()]));

        if (!$this->organization->save()) {
            return false;
        }

        Event::fire(new OnOrganizationCreate($this->organization));

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
        return $this->organization->fill($data)->save();
    }

    /**
     * Удалить запись
     *
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        return $this->organization->delete();
    }

    /**
     * Приглашение исполнителя в организацию
     *
     * @param array $data
     * @return bool
     */
    public function inviteUserToOrganization(array $data): bool
    {
        return (new UserOrganization($data))->save();
    }

    /**
     * Получить модель
     *
     * @return Organization|Model
     */
    public function getModel(): Model
    {
        return $this->organization;
    }
}