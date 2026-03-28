<?php

namespace App\Modules\Product\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Product\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="List categories",
     *     @OA\Response(response=200, description="List of categories")
     * )
     */
    public function index(): JsonResponse
    {
        $categories = Category::active()->with('children')->root()->get();
        return $this->successResponse($categories);
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Get a category",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category data"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = Category::with(['children', 'products'])->find($id);
        if (!$category) {
            return $this->errorResponse('Category not found', 404);
        }
        return $this->successResponse($category);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Create a category",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="parent_id", type="integer"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'parent_id'   => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['tenant_id'] = $request->header('X-Tenant-ID', 1);

        $category = Category::create($validated);
        return $this->successResponse($category, 'Category created', 201);
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category updated")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'parent_id'   => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['nullable', 'integer'],
        ]);
        $category->update($validated);
        return $this->successResponse($category, 'Category updated');
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category deleted")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        Category::findOrFail($id)->delete();
        return $this->successResponse(null, 'Category deleted');
    }
}
