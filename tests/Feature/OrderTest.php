<?php

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateOrder()
    {
        $response = $this->postJson('/api/orders', [
            'order_number' => '12345',
            'total_amount' => 100.50,
            'items' => [
                ['product_name' => 'Product A', 'quantity' => 1, 'price' => 50.25],
                ['product_name' => 'Product B', 'quantity' => 2, 'price' => 50.25],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'order_number', 'status', 'total_amount', 'items' => [
                    '*' => ['id', 'product_name', 'quantity', 'price'],
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'order_number' => '12345',
            'total_amount' => 100.50,
        ]);

        $order = Order::where('order_number', '12345')->first();
        $this->assertCount(2, $order->items);
    }
}
