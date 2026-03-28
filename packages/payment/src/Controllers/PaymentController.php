<?php

namespace ECommerce\Payment\Controllers;

use ECommerce\Core\Traits\ApiResponse;
use ECommerce\Order\Models\Order;
use ECommerce\Payment\Models\Payment;
use ECommerce\Payment\Requests\PaymentRequest;
use ECommerce\Payment\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * @OA\Post(
     *     path="/payments/process",
     *     tags={"Payments"},
     *     summary="Process a payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"order_id","payment_method_id"},
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(property="payment_method_id", type="string"),
     *             @OA\Property(property="gateway", type="string", default="stripe")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Payment processed"),
     *     @OA\Response(response=400, description="Payment failed")
     * )
     */
    public function process(PaymentRequest $request): JsonResponse
    {
        try {
            $order = Order::findOrFail($request->order_id);
            $payment = $this->paymentService->processPayment(
                order: $order,
                paymentData: $request->only(['payment_method_id', 'return_url']),
                gatewayName: $request->get('gateway', 'stripe'),
            );
            $success = $payment->status === Payment::STATUS_PAID;
            return $this->successResponse($payment, $success ? 'Payment processed successfully' : 'Payment failed', $success ? 200 : 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/payments/webhook",
     *     tags={"Payments"},
     *     summary="Handle payment webhook",
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=200, description="Webhook processed")
     * )
     */
    public function webhook(Request $request): JsonResponse
    {
        // Handle Stripe webhook events
        $payload = $request->all();
        $event = $payload['type'] ?? null;

        switch ($event) {
            case 'payment_intent.succeeded':
                $transactionId = $payload['data']['object']['id'] ?? null;
                if ($transactionId) {
                    Payment::where('transaction_id', $transactionId)->update(['status' => Payment::STATUS_PAID]);
                }
                break;
            case 'payment_intent.payment_failed':
                $transactionId = $payload['data']['object']['id'] ?? null;
                if ($transactionId) {
                    Payment::where('transaction_id', $transactionId)->update(['status' => Payment::STATUS_FAILED]);
                }
                break;
        }

        return $this->successResponse(null, 'Webhook processed');
    }
}
