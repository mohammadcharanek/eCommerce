<?php

namespace App\Modules\Coupon\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Coupon\Requests\StoreCouponRequest;
use App\Modules\Coupon\Resources\CouponResource;
use App\Modules\Coupon\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CouponController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CouponService $couponService) {}

    /**
     * @OA\Get(
     *     path="/coupons",
     *     tags={"Coupons"},
     *     summary="List coupons",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of coupons")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $paginator = $this->couponService->getAll((int) $request->get('per_page', 15));
        return $this->paginatedResponse($paginator, CouponResource::collection($paginator));
    }

    /**
     * @OA\Post(
     *     path="/coupons",
     *     tags={"Coupons"},
     *     summary="Create a coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"code","type","value"},
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="type", type="string", enum={"percentage","fixed"}),
     *             @OA\Property(property="value", type="number")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Coupon created")
     * )
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), ['tenant_id' => $request->header('X-Tenant-ID', 1)]);
        $coupon = $this->couponService->create($data);
        return $this->successResponse(new CouponResource($coupon), 'Coupon created', 201);
    }

    /**
     * @OA\Put(
     *     path="/coupons/{id}",
     *     tags={"Coupons"},
     *     summary="Update a coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Coupon updated")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'code'             => ['nullable', 'string', 'max:50'],
            'type'             => ['nullable', 'in:percentage,fixed'],
            'value'            => ['nullable', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses'         => ['nullable', 'integer', 'min:1'],
            'starts_at'        => ['nullable', 'date'],
            'expires_at'       => ['nullable', 'date'],
            'is_active'        => ['boolean'],
        ]);
        $coupon = $this->couponService->update($id, $validated);
        return $this->successResponse(new CouponResource($coupon), 'Coupon updated');
    }

    /**
     * @OA\Delete(
     *     path="/coupons/{id}",
     *     tags={"Coupons"},
     *     summary="Delete a coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Coupon deleted")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->couponService->delete($id);
        return $this->successResponse(null, 'Coupon deleted');
    }

    /**
     * @OA\Post(
     *     path="/coupons/validate",
     *     tags={"Coupons"},
     *     summary="Validate a coupon",
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"code","order_amount"},
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="order_amount", type="number")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Coupon valid"),
     *     @OA\Response(response=400, description="Coupon invalid")
     * )
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code'         => ['required', 'string'],
            'order_amount' => ['required', 'numeric', 'min:0'],
        ]);
        try {
            $result = $this->couponService->apply($request->code, (float) $request->order_amount);
            return $this->successResponse([
                'coupon'   => new CouponResource($result['coupon']),
                'discount' => $result['discount'],
            ], 'Coupon is valid');
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
