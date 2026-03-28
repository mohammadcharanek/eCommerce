<?php

namespace Ecommerce\Orders\Http\Controllers;

use Ecommerce\Orders\Http\Requests\StoreOrderRequest;
use Ecommerce\Orders\Http\Requests\UpdateOrderStatusRequest;
use Ecommerce\Orders\Models\Order;
use Ecommerce\Orders\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search', 'user_id']);
        $orders  = $this->orderService->list($filters);

        return view('orders::orders.index', compact('orders', 'filters'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        return view('orders::orders.create');
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $items     = $validated['items'] ?? [];
        unset($validated['items']);

        $order = $this->orderService->create($validated, $items);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully.');
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['items', 'payment']);

        return view('orders::orders.show', compact('order'));
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order = $this->orderService->updateStatus($order, $request->validated('status'));

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order status updated to [{$order->status}].");
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        $this->orderService->cancel($order);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order cancelled successfully.');
    }
}
