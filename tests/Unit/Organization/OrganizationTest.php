<?php

namespace Tests\Unit\Organization;

use App\Events\OnOrganizationCreate;
use App\Models\Db\User;
use App\Repositories\Organization\OrganizationRepository;
use App\Services\Organization\OrganizationService;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CustomTestCase;

/**
 * Class OrganizationTest
 * @package Tests\Unit\Organization
 */
class OrganizationTest extends CustomTestCase
{
    use DatabaseTransactions;

    /**
     *  Айдишка овнера
     *
     * @const
     */
    const OWNER_ID = 1;

    /**
     * Айдишка перформера
     *
     * @const
     */
    const PERFORMER_ID = 22;

    /**
     * @const
     */
    const ORGANIZATION_API_URL = 'api/organization/';

    /**
     * @const
     */
    const ORGANIZATION_API_CREATE_METHOD = 'create';
    const ORGANIZATION_API_DELETE_METHOD = 'delete';
    const ORGANIZATION_API_INVITE_METHOD = 'invite';

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var Generator
     */
    private $factory;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->organizationRepository = new OrganizationRepository();
        $this->factory = Factory::create();
    }

    /**
     * Проверка срабатывания ивента при создании организации
     *
     * @return void
     * @throws Exception
     */
    public function testOnOrganizationCreateEvent(): void
    {
        $this->expectsEvents(OnOrganizationCreate::class);
        /** @var Authenticatable $user */
        $user = User::findOrFail(self::OWNER_ID);
        $this->actingAs($user);
        $this->organizationRepository->create([
            'organization_name' => $this->factory->company,
            'registration_date' => $this->factory->date(),
        ]);
    }

    /**
     * Попытка создать организацию будучи исполнителем(получаем экзепшн)
     *
     * @return void
     */
    public function testCreateOrganizationByPerformer(): void
    {
        /** @var Authenticatable $user */
        $user = User::findOrFail(self::PERFORMER_ID);
        $this->apiAs($user,
            'POST',
            self::ORGANIZATION_API_URL . self::ORGANIZATION_API_CREATE_METHOD,
            [
                'organization_name' => $this->factory->company,
                'registration_date' => $this->factory->date()
            ])
            ->assertStatus(500);
    }

    /**
     * Попытка удалить чужую организацию под, будучи овнером (ловим экзепшн)
     *
     * @return void
     */
    public function testDeleteOtherOwnersOrganization(): void
    {
        $this->createOrganization();
        // Новосозданная организация
        $organization = $this->organizationRepository->getModel();
        $otherOwner = User::query()
            ->where([['role', '=', User::ROLE_OWNER], ['id', '!=', self::OWNER_ID]])
            ->get()
            ->first();

        // Логинимся другим овнером, и пытаемся удалить организацию
        $this->apiAs($otherOwner,
            'DELETE',
            self::ORGANIZATION_API_URL . self::ORGANIZATION_API_DELETE_METHOD . '/' . $organization->id
        )
            ->assertStatus(500);
    }

    /**
     * Попытка пригласить юзера в организацию другого овнера(ловим экзепшн)
     *
     * @return void
     */
    public function testInviteToOtherOwnersOrganization(): void
    {
        $this->createOrganization();
        $otherOwner = User::query()
            ->where([['role', '=', User::ROLE_OWNER], ['id', '!=', self::OWNER_ID]])
            ->get()
            ->first();

        // Логинимся другим овнером, и пытаемся удалить организацию
        $this->apiAs($otherOwner,
            'POST',
            self::ORGANIZATION_API_URL . self::ORGANIZATION_API_INVITE_METHOD,
            [
                'user_id'         => self::PERFORMER_ID,
                'organization_id' => $this->organizationRepository->getModel()->id,
            ])
            ->assertStatus(500);
    }

    /**
     * Попытка пригласить другого овнера в организацию (ловим экзепшн)
     *
     * @return void
     */
    public function testInviteOtherOwnerToOrganization(): void
    {
        $this->createOrganization();
        $otherOwner = User::query()
            ->where([['role', '=', User::ROLE_OWNER], ['id', '!=', self::OWNER_ID]])
            ->get()
            ->first();

        $this->apiAs($otherOwner,
            'POST',
            self::ORGANIZATION_API_URL . self::ORGANIZATION_API_INVITE_METHOD,
            [
                'user_id'         => $otherOwner->id,
                'organization_id' => $this->organizationRepository->getModel()->id,
            ])
            ->assertStatus(500);
    }

    /**
     * Попытка пригласить ранее приглашенного исполнителя (ловим экзепшн)
     *
     * @return void
     */
    public function testInviteAlreadyInvitedPerformer(): void
    {
        $this->createOrganization();

        // Приглашаем
        $this->organizationRepository->inviteUserToOrganization([
            'user_id'         => self::PERFORMER_ID,
            'organization_id' => $this->organizationRepository->getModel()->id,
        ]);

        //  и еще раз приглашаем
        $this->apiAs(User::findOrFail(self::OWNER_ID),
            'POST',
            self::ORGANIZATION_API_URL . self::ORGANIZATION_API_INVITE_METHOD,
            [
                'user_id'         => self::PERFORMER_ID,
                'organization_id' => $this->organizationRepository->getModel()->id,
            ])
            ->assertStatus(500);
    }

    /**
     * Попытка пригласить пользователя в организацию(должно вернуть true)
     *
     * @return void
     */
    public function testInvitePerformerToOrganization(): void
    {
        $this->createOrganization();

        $this->assertTrue($this->organizationRepository->inviteUserToOrganization([
            'user_id'         => self::PERFORMER_ID,
            'organization_id' => $this->organizationRepository->getModel()->id,
        ]));
    }

    /**
     * Создает организацию с тестового овнера
     *
     * @return void
     */
    private function createOrganization(): void
    {
        /** @var Authenticatable $user */
        $user = User::findOrFail(self::OWNER_ID);
        // Логинимся под тестовым овнером и создаем организацию
        $this->actingAs($user);
        $this->organizationRepository->create([
            'organization_name' => $this->factory->company,
            'registration_date' => $this->factory->date(),
        ]);
    }
}
