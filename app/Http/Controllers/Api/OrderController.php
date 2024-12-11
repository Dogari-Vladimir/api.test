<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_number' => 'required|string|unique:orders,order_number',
            'total_amount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        $order = Order::create([
            'order_number' => $data['order_number'],
            'status' => 'pending',
            'total_amount' => $data['total_amount'],
        ]);

        foreach ($data['items'] as $item) {
            $order->items()->create($item);
        }

        return response()->json($order->load('items'), 201);
    }

    public function show($orderNumber): JsonResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        return response()->json($order);
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $query = Order::query();

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json($query->with('items')->get());
    }

    public function externalApi(): JsonResponse
    {
        $order = Order::where('order_number', '12345')->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->update(['status' => 'shipped']);

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
        ]);
    }
}
