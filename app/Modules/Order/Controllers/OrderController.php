<?php

namespace App\Modules\Order\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Cart\Models\Cart;
use App\Modules\Order\Requests\StoreOrderRequest;
use App\Modules\Order\Resources\OrderResource;
use App\Modules\Order\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OrderService $orderService) {}

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="List user orders",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of orders")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->orderService->getUserOrders($request->user()->id);
        return $this->paginatedResponse($paginator, OrderResource::collection($paginator));
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     tags={"Orders"},
     *     summary="Get an order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order data"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrder($id);
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }
        return $this->successResponse(new OrderResource($order->load(['items', 'payments', 'user'])));
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Create an order from cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"cart_id","shipping_address"},
     *             @OA\Property(property="cart_id", type="integer"),
     *             @OA\Property(property="shipping_address", type="object")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $cart = Cart::findOrFail($request->cart_id);
            $order = $this->orderService->createOrder(
                cart: $cart,
                userId: $request->user()->id,
                addressData: $request->only(['shipping_address', 'billing_address']),
                currency: $request->get('currency', 'USD'),
            );
            return $this->successResponse(new OrderResource($order), 'Order created successfully', 201);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Update order status",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"status"}, @OA\Property(property="status", type="string"))
     *     ),
     *     @OA\Response(response=200, description="Status updated")
     * )
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => ['required', 'string']]);
        try {
            $order = $this->orderService->updateStatus($id, $request->status);
            return $this->successResponse(new OrderResource($order), 'Order status updated');
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}/cancel",
     *     tags={"Orders"},
     *     summary="Cancel an order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order cancelled")
     * )
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->cancelOrder($id);
            return $this->successResponse(new OrderResource($order), 'Order cancelled');
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
