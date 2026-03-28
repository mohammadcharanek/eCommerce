<?php

namespace Ecommerce\Payments\Http\Controllers;

use Ecommerce\Orders\Models\Order;
use Ecommerce\Payments\Http\Requests\ProcessPaymentRequest;
use Ecommerce\Payments\Http\Requests\RefundPaymentRequest;
use Ecommerce\Payments\Models\Payment;
use Ecommerce\Payments\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $filters  = $request->only(['status', 'payment_method', 'order_id']);
        $payments = $this->paymentService->list($filters);

        return view('payments::payments.index', compact('payments', 'filters'));
    }

    /**
     * Show the payment form for an order.
     */
    public function create(Order $order)
    {
        if ($order->isPaid()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('info', 'This order has already been paid.');
        }

        $methods = config('payments.methods', []);

        return view('payments::payments.create', compact('order', 'methods'));
    }

    /**
     * Process a payment for the given order.
     */
    public function store(ProcessPaymentRequest $request, Order $order)
    {
        $payment = $this->paymentService->process($order, $request->validated());

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Payment processed successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('order');

        return view('payments::payments.show', compact('payment'));
    }

    /**
     * Process a refund for the given payment.
     */
    public function refund(RefundPaymentRequest $request, Payment $payment)
    {
        $amount  = $request->validated('amount');
        $payment = $this->paymentService->refund($payment, $amount);

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Refund processed successfully.');
    }
}
