<?php

namespace App\Api\V1\Http\Controllers\Auth;

use JWTAuth;
use Dingo\Api\Http\Response;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Transformers\UserTransformer;
use App\Api\V1\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Auth\LoginRequest;
use App\Contracts\AuthenticationServiceInterface;
use App\Api\V1\Http\Requests\Auth\RegistrationRequest;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * UserController constructor.
     * @param AuthenticationServiceInterface $authService
     */
    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @SWG\Post(
     *    path = "/api/auth/login",
     *    tags={"Authentication"},
     *    description="Sing in",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/LoginRequest"),
     *    ),
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success",
     *       @SWG\Schema(ref="#/definitions/Token")
     *    ),
     *    @SWG\Response(
     *       response = 202,
     *       description = "Invalid Credentials!",
     *    ),
     *    @SWG\Response(
     *       response = 422,
     *       description = "Validation errors",
     *       @SWG\Schema(ref="#/definitions/ValidationErrors")
     *    ),
     * ),
     *
     * @SWG\Definition(
     *    definition="LoginRequest",
     *    type="object",
     *    @SWG\Property(property="email", type="string"),
     *    @SWG\Property(property="password", type="string")
     * ),
     *
     * @SWG\Definition(
     *    definition="Token",
     *    type="object",
     *    @SWG\Property(property="token", type="string")
     * )
     *
     * Login action
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!($token = $this->authService->login($request->validated()))) {
            $this->response->error('Invalid Credentials', 202);
        }

        return response()->json(['token' => $token]);
    }

    /**
     * @SWG\Post(
     *    path = "/api/auth/register",
     *    tags={"Authentication"},
     *    description="User registration",
     *    produces={"application/json"},
     *    @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       required=true,
     *       @SWG\Schema(ref="#/definitions/RegistrationRequest"),
     *     ),
     *     @SWG\Response(
     *        response = 200,
     *        description = "Success",
     *        @SWG\Schema(ref="#/definitions/User")
     *     ),
     *     @SWG\Response(
     *        response = 400,
     *        description = "Unable to register user!",
     *     ),
     *     @SWG\Response(
     *        response = 422,
     *        description = "Validation errors",
     *        @SWG\Schema(ref="#/definitions/ValidationErrors")
     *     ),
     * ),
     *
     * @SWG\Definition(
     *    definition="RegistrationRequest",
     *    type="object",
     *    @SWG\Property(property="surname", type="string"),
     *    @SWG\Property(property="name", type="string"),
     *    @SWG\Property(property="email", type="string"),
     *    @SWG\Property(property="password", type="string"),
     *    @SWG\Property(property="password_confirm", type="string"),
     *    @SWG\Property(property="role", type="integer", enum={"1","2"})
     * ),
     * @SWG\Definition(
     *    definition="User",
     *    type="object",
     *    @SWG\Property(property="id", type="integer"),
     *    @SWG\Property(property="surname", type="string"),
     *    @SWG\Property(property="name", type="string"),
     *    @SWG\Property(property="email", type="string"),
     *    @SWG\Property(property="registration_date", type="string"),
     *    @SWG\Property(property="role", type="integer")
     * )
     *
     * User registration action
     *
     * @param RegistrationRequest $request
     * @return Response
     */
    public function register(RegistrationRequest $request): Response
    {
        if (!$this->authService->register($request->validated())) {
            $this->response->error('Unable to register user!', 400);
        }

        return $this->response->item($this->authService->getUser(), new UserTransformer());
    }

    /**
     * @SWG\Post(
     *    path = "/api/auth/logout",
     *    tags={"Authentication"},
     *    description="Logout",
     *    produces={"application/json"},
     *    @SWG\Response(
     *       response = 200,
     *       description = "Success"
     *    ),
     *    @SWG\Response(
     *       response = 401,
     *       description = "Token not provided",
     *       @SWG\Schema(ref="#/definitions/Error")
     *    ),
     *    security={{
     *      "auth":{}
     *    }}
     * )
     *
     * Logout Action
     *
     * @return Response
     */
    public function logout(): Response
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return $this->response->noContent()->setStatusCode(200);
    }
}
