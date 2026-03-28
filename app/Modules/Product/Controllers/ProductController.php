<?php

namespace App\Modules\Product\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Product\Requests\StoreProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Modules\Product\Resources\ProductCollection;
use App\Modules\Product\Resources\ProductResource;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ProductService $productService) {}

    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Products"},
     *     summary="List products",
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $paginator = $this->productService->listProducts($request->only(['search', 'category_id']), $perPage);
        return $this->paginatedResponse($paginator, new ProductCollection($paginator));
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Get a product",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Product data"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $product = $this->productService->getProduct($id);
        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }
        return $this->successResponse(new ProductResource($product));
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Products"},
     *     summary="Create a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")),
     *     @OA\Response(response=201, description="Product created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), [
            'tenant_id' => $request->header('X-Tenant-ID', 1),
        ]);
        $product = $this->productService->createProduct($data);
        return $this->successResponse(new ProductResource($product), 'Product created', 201);
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product updated"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->validated());
        return $this->successResponse(new ProductResource($product), 'Product updated');
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return $this->successResponse(null, 'Product deleted');
    }
}
