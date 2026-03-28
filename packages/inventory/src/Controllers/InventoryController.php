<?php

namespace ECommerce\Inventory\Controllers;

use ECommerce\Core\Traits\ApiResponse;
use ECommerce\Inventory\Models\Inventory;
use ECommerce\Inventory\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InventoryController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly InventoryService $inventoryService) {}

    /**
     * @OA\Get(
     *     path="/inventory/{productId}",
     *     tags={"Inventory"},
     *     summary="Get inventory for a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Inventory data")
     * )
     */
    public function show(int $productId): JsonResponse
    {
        $inventory = Inventory::where('product_id', $productId)->with(['product', 'variant'])->get();
        return $this->successResponse($inventory);
    }

    /**
     * @OA\Put(
     *     path="/inventory/{productId}",
     *     tags={"Inventory"},
     *     summary="Update inventory for a product",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="productId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="variant_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Inventory updated")
     * )
     */
    public function update(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate([
            'quantity'   => ['required', 'integer', 'min:0'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'warehouse'  => ['nullable', 'string'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);
        $inventory = $this->inventoryService->updateStock($productId, $validated['quantity'], $validated['variant_id'] ?? null);
        return $this->successResponse($inventory, 'Inventory updated');
    }
}
