<?php

namespace Tests\Unit\Task;

use App\Events\OnSetTaskPerformer;
use App\Models\Db\Organization;
use App\Models\Db\Task;
use App\Models\Db\User;
use App\Models\Db\UserOrganization;
use App\Repositories\Task\TaskRepository;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CustomTestCase;

/**
 * Class TaskTest
 * @package Tests\Unit\Task
 */
class TaskTest extends CustomTestCase
{
    use DatabaseTransactions;

    /**
     * @const
     */
    const TASK_API_URL = 'api/task/';

    /**
     * @const
     */
    const TASK_API_CREATE_METHOD = 'create';
    const TASK_API_CHANGE_STATUS_METHOD = 'change-status';
    const TASK_API_SET_PERFORMER_METHOD = 'set-performer';

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var Generator
     */
    private $faker;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->taskRepository = new TaskRepository();
        $this->faker = Factory::create();
    }

    /**
     * Попытка создать задачу, будучи исполнителем (ловим экзепшн)
     *
     * @return void
     */
    public function testCreateTaskAuthorizedAsPerformer(): void
    {
        $this->apiAs($this->getFirstPerformer(),
            'POST',
            self::TASK_API_URL . self::TASK_API_CREATE_METHOD,
            [
                'organization_id' => rand(1, 100),
                'name'            => $this->faker->words(3, true),
                'deadline'        => '2100-02-01'
            ])
            ->assertStatus(500);
    }

    /**
     * Попытка создать задачу от другой организации, не будучи ее овнером
     *
     * @return void
     */
    public function testCreateTaskByOwnerWithWrongOrganization(): void
    {
        $owner = $this->getFirstOwner();
        $this->apiAs(
            $owner,
            'POST',
            self::TASK_API_URL . self::TASK_API_CREATE_METHOD,
            [
                'organization_id' => Organization::query()
                    ->where('owner_id', '!=', $owner->id)
                    ->first()->id,
                'name'            => $this->faker->words(3, true),
                'deadline'        => '2100-01-01'
            ]
        )->assertStatus(500);
    }

    /**
     * Попытка создать задачу на исполнителя,
     * который не пренадлежит организации текущего овнера(ловим экзепшн)
     *
     * @return void
     */
    public function testCreateTaskForPerformerFromOtherOrganization(): void
    {
        $userOrganisation = UserOrganization::query()->first();
        $this->apiAs($userOrganisation->organization->owner,
            'POST',
            self::TASK_API_URL . self::TASK_API_CREATE_METHOD,
            [
                'organization_id' => $userOrganisation->organization_id,
                'name'            => $this->faker->words(3, true),
                'performer_id'    => UserOrganization::query()
                    ->where([
                        ['user_id', '!=', $userOrganisation->user_id],
                        ['organization_id', '!=', $userOrganisation->organization_id],
                    ])->first()->user_id,
                'deadline'        => '2100-01-01',
            ]
        )->assertStatus(500);
    }

    /**
     * Создаем нормальную задачу на подходящего исполнителя (должно вернуть true)
     *
     * @return void
     */
    public function testCreateTaskForPerformerFromValidOrganization(): void
    {
        $userOrganisation = UserOrganization::query()->first();
        $this->apiAs(
            $userOrganisation->organization->owner,
            'POST',
            self::TASK_API_URL . self::TASK_API_CREATE_METHOD,
            [
                'organization_id' => $userOrganisation->organization_id,
                'name'            => $this->faker->words(3, true),
                'performer_id'    => $userOrganisation->user_id,
                'deadline'        => '2100-01-01',
            ]
        )->assertStatus(200);
    }

    /**
     * Отлов ивента при создании задачи на юзера
     *
     * @return void
     * @throws \Exception
     */
    public function testCheckForEventWhileCreatingTask(): void
    {
        $this->expectsEvents(OnSetTaskPerformer::class);
        $userOrganisation = UserOrganization::query()->first();
        $this->apiAs(
            $userOrganisation->organization->owner,
            'POST',
            self::TASK_API_URL . self::TASK_API_CREATE_METHOD,
            [
                'organization_id' => $userOrganisation->organization_id,
                'name'            => $this->faker->words(3, true),
                'performer_id'    => $userOrganisation->user_id,
                'deadline'        => '2100-01-01',
            ]
        )->assertStatus(200);
    }

    /**
     * Отлов ивента при попытке назначить задачу на валидного исполнителя
     *
     * @return void
     * @throws \Exception
     */
    public function testCheckEventWhileSettingTaskToPerformer(): void
    {
        $this->expectsEvents(OnSetTaskPerformer::class);
        $userOrganisation = UserOrganization::query()->first();
        $this->actingAs($userOrganisation->organization->owner);
        $this->taskRepository->create([
            'organization_id' => $userOrganisation->organization_id,
            'name'            => $this->faker->words(3, true),
            'deadline'        => '2100-01-01',
        ]);

        $this->apiAs(
            $userOrganisation->organization->owner,
            'PUT',
            self::TASK_API_URL . self::TASK_API_SET_PERFORMER_METHOD . '/' . $this->taskRepository->getModel()->id,
            ['performer_id' => $userOrganisation->user_id]
        )->assertStatus(200);
    }

    /**
     * Попытка назначить задачу на юзера, который уже ее выполняет (ловим экзепшн)
     *
     * @return void
     */
    public function testSetPerformerWhichAlreadyPerformsTheTask(): void
    {
        /** @var UserOrganization $userOrganisation */
        $userOrganisation = UserOrganization::query()->first();
        $this->actingAs($userOrganisation->organization->owner);
        $this->taskRepository->create([
            'organization_id' => $userOrganisation->organization_id,
            'name'            => $this->faker->words(3, true),
            'performer_id'    => $userOrganisation->user_id,
            'deadline'        => '2100-01-01',
        ]);

        $this->apiAs(
            $userOrganisation->organization->owner,
            'PUT',
            self::TASK_API_URL . self::TASK_API_SET_PERFORMER_METHOD . '/' . $this->taskRepository->getModel()->id,
            ['performer_id' => $userOrganisation->user_id]
        )->assertStatus(500);
    }

    /**
     * Попытка назначить задачу будучи исполнителем(ловим экзепшн)
     *
     * @return void
     */
    public function testSetPerformerBeingAuthorizedAsPerformer(): void
    {
        $userOrganisation = UserOrganization::query()->first();
        $this->actingAs($userOrganisation->organization->owner);

        $this->taskRepository->create([
            'organization_id' => $userOrganisation->organization_id,
            'name'            => $this->faker->words(3, true),
            'performer_id'    => $userOrganisation->user_id,
            'deadline'        => '2100-01-01',
        ]);

        // Создали организацию будучи овнером и перелогинились
        $this->apiAs(
            $userOrganisation->organization->owner,
            'PUT',
            self::TASK_API_URL . self::TASK_API_SET_PERFORMER_METHOD . '/' . $this->taskRepository->getModel()->id,
            ['performer_id' => $userOrganisation->user_id]
        )->assertStatus(500);
    }

    /**
     * Попытка изменить статус задачи будучи исполнителем
     *
     * @return void
     */
    public function testUpdateTaskStatusByPerformer(): void
    {
        $userOrganisation = UserOrganization::query()->first();
        $this->actingAs($userOrganisation->organization->owner);

        $this->taskRepository->create([
            'organization_id' => $userOrganisation->organization_id,
            'name'            => $this->faker->words(3, true),
            'performer_id'    => $userOrganisation->user_id,
            'deadline'        => '2100-01-01',
        ]);

        $this->apiAs(
            $this->taskRepository->getModel()->performer,
            'PUT',
            self::TASK_API_URL . self::TASK_API_CHANGE_STATUS_METHOD . '/' . $this->taskRepository->getModel()->id,
            ['status' => Task::STATUS_DONE]
        )->assertStatus(200);
    }

    /**
     * Попытка изменить статус задачи будучи исполнителем(ловим эксепшн)
     *
     * @return void
     */
    public function testUpdateTaskStatusNotByPerformer(): void
    {
        $userOrganisation = UserOrganization::query()->first();
        $this->actingAs($userOrganisation->organization->owner);

        $this->taskRepository->create([
            'organization_id' => $userOrganisation->organization_id,
            'name'            => $this->faker->words(3, true),
            'performer_id'    => $userOrganisation->user_id,
            'deadline'        => '2100-01-01',
        ]);
        $this->apiAs(
            $userOrganisation->organization->owner,
            'PUT',
            self::TASK_API_URL . self::TASK_API_CHANGE_STATUS_METHOD . '/' . $this->taskRepository->getModel()->id,
            ['status' => Task::STATUS_DONE]
        )->assertStatus(500);
    }

    /**
     * Получить первого руководителя
     *
     * @return Builder|Authenticatable|User
     */
    private function getFirstOwner()
    {
        return User::query()->where('role', '=', User::ROLE_OWNER)->first();
    }

    /**
     * Получить первого исполнителя
     *
     * @return Builder|Authenticatable|User
     */
    private function getFirstPerformer()
    {
        return User::query()->where('role', '=', User::ROLE_PERFORMER)->first();
    }
}
