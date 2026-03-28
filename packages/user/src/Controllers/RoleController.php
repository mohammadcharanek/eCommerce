<?php

namespace ECommerce\User\Controllers;

use ECommerce\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        return $this->successResponse(Role::with('permissions')->get());
    }
}
