<?php
/**
 * Created by PhpStorm.
 * User: manowartop
 * Date: 03.08.2018
 * Time: 22:54
 */

namespace App\Api\V1\Http\Controllers\Organization;

use Exception;
use Dingo\Api\Http\Response;
use InvalidArgumentException;
use App\Models\Db\Organization;
use App\Contracts\RepositoryInterface;
use App\Api\V1\Http\Controllers\Controller;
use App\Transformers\OrganizationTransformer;
use App\Contracts\CheckOrganizationPermissionsInterface;
use App\Repositories\Organization\OrganizationRepository;
use App\Api\V1\Http\Requests\Organization\InvitationRequest;
use App\Api\V1\Http\Requests\Organization\OrganizationRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Контроллер CRUD организаций
 *
 * Class OrganizationController
 * @package App\Http\Controllers\Api
 */
class OrganizationController extends Controller
{
    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var CheckOrganizationPermissionsInterface
     */
    private $permission;

    /**
     * OrganizationController constructor.
     * @param RepositoryInterface $organizationRepository
     * @param CheckOrganizationPermissionsInterface $permission
     */
    public function __construct(RepositoryInterface $organizationRepository, CheckOrganizationPermissionsInterface $permission)
    {
        $this->middleware('jwt.auth');

        $this->organizationRepository = $organizationRepository;
        $this->permission = $permission;
    }

    /**
     * @SWG\Get(
     *    path = "/api/organization/get-all",
     *    tags={"Organization"},
     *    description="Get list or Owner/Performer organizations",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="page",
     *       in="query",
     *       type="integer",
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Organizations")
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
     *    definition="Organizations",
     *    type="object",
     *    @SWG\Property(property="data", type="array", @SWG\Items(ref="#/definitions/Organization")),
     *    @SWG\Property(property="meta", type="object", ref="#/definitions/Pagination"))
     * ),
     * @SWG\Definition(
     *     definition="Organization",
     *     type="object",
     *     @SWG\Property(property="id", type="integer"),
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="registration_date", type="string"),
     *     @SWG\Property(property="owner", type="object", ref="#/definitions/User"))
     * )
     *
     * Пагинированный список организаций
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->response->paginator($this->organizationRepository->getAll(), new OrganizationTransformer());
    }

    /**
     * @SWG\Get(
     *    path = "/api/organization/get/{id}",
     *    tags={"Organization"},
     *    description="Get organization by id",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="id",
     *       in="path",
     *       type="integer",
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Organization")
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
     *       description = "User cant get record of this organization",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * )
     *
     * Получение записи организации
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function get(int $id): Response
    {
        $organization = Organization::findOrFail($id);
        // Проверки
        $this->permission->checkGetPermission($organization);
        return $this->response->item($organization, new OrganizationTransformer());
    }

    /**
     * @SWG\Post(
     *    path = "/api/organization/create",
     *    tags={"Organization"},
     *    description="Create organization",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/OrganizationRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Organization")
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while creating an organization",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "User is not an owner",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="OrganizationRequest",
     *     type="object",
     *     @SWG\Property(property="organization_name", type="string"),
     *     @SWG\Property(property="registration_date", type="string")
     * )
     *
     * Создание организации
     *
     * @param OrganizationRequest $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function create(OrganizationRequest $request): Response
    {
        $this->permission->checkCreatePermission();

        if (!$this->organizationRepository->create($request->validated())) {
            $this->response->error('Error while creating an organization', 400);
        }

        return $this->response->item($this->organizationRepository->getModel(), new OrganizationTransformer());
    }

    /**
     * @SWG\Put(
     *    path = "/api/organization/update/{id}",
     *    tags={"Organization"},
     *    description="Update organization",
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
     *       @SWG\Schema(ref="#/definitions/OrganizationRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Organization")
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while updating an organization",
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
     * Обновление организации
     *
     * @param int $id
     * @param OrganizationRequest $request
     * @return Response
     * @throws InvalidArgumentException
     */
    public function update(int $id, OrganizationRequest $request): Response
    {
        $organization = Organization::findOrFail($id);
        $this->permission->checkUpdatePermission($organization);

        if (!$this->organizationRepository->setModel($organization)->update($request->validated())) {
            $this->response->error('Error while updating an organization', 400);
        }

        return $this->response->item($this->organizationRepository->getModel(), new OrganizationTransformer());
    }

    /**
     * @SWG\Delete(
     *    path = "/api/organization/delete/{id}",
     *    tags={"Organization"},
     *    description="Delete organization",
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
     *       description = "Error while deleting an organization",
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
     * Удаление организации
     *
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $organization = Organization::findOrFail($id);
        $this->permission->checkDeletePermission($organization);

        if (!$this->organizationRepository->setModel($organization)->delete()) {
            $this->response->error('Error while deleting an organization', 400);
        }

        return $this->response->noContent()->setStatusCode(200);
    }

    /**
     * @SWG\Post(
     *    path = "/api/organization/invite",
     *    tags={"Organization"},
     *    description="Invite user to organisation",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/InvitationRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *    ),
     *    @SWG\Response(
     *       response = 400,
     *       description = "Error while inviting to organization",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    @SWG\Response(
     *       response = 500,
     *       description = "Unable to invite user|No query results for model",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * ),
     *
     * @SWG\Definition(
     *     definition="InvitationRequest",
     *     type="object",
     *     @SWG\Property(property="user_id", type="integer"),
     *     @SWG\Property(property="organization_id", type="integer")
     * )
     *
     * Приглашение юзера в организацию
     *
     * @param InvitationRequest $request
     * @return Response
     * @throws Exception
     */
    public function inviteToOrganization(InvitationRequest $request): Response
    {
        $this->permission->checkInvitePermission($request->get('user_id'), $request->get('organization_id'));

        if (!$this->organizationRepository->inviteUserToOrganization($request->validated())) {
            $this->response->error("Error while inviting to organization", 400);
        }

        return $this->response->noContent()->setStatusCode(200);
    }
}