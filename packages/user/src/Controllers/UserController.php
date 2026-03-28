<?php

namespace ECommerce\User\Controllers;

use ECommerce\Core\Traits\ApiResponse;
use ECommerce\User\Resources\UserResource;
use ECommerce\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly UserService $userService) {}

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="List users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of users")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->userService->getAll((int) $request->get('per_page', 15));
        return $this->paginatedResponse($paginator, UserResource::collection($paginator));
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Get a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User data"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getById($id);
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        return $this->successResponse(new UserResource($user));
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=200, description="User updated")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['nullable', 'string', 'max:255'],
            'email'    => ['nullable', 'email', "unique:users,email,{$id}"],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => ['nullable', 'string', 'exists:roles,name'],
        ]);
        $user = $this->userService->update($id, $validated);
        return $this->successResponse(new UserResource($user), 'User updated');
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->userService->delete($id);
        return $this->successResponse(null, 'User deleted');
    }
}
