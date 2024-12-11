<?php

namespace Tests\Feature;

use App\Jobs\UpdateOrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function it_updates_order_status_through_job()
    {
        $order = Order::factory()->create([
            'order_number' => '12345',
            'status' => 'pending',
        ]);

        Http::fake([
            route('order.externalApi') => Http::response([
                'order_number' => '12345',
                'status' => 'shipped',
            ], 200),
        ]);

        (new UpdateOrderStatus())->handle();
        $order->refresh();
        $this->assertEquals('shipped', $order->status);
    }

    /** @test */
    public function it_does_not_update_status_if_api_fails()
    {
        $order = Order::factory()->create([
            'order_number' => '12345',
            'status' => 'pending',
        ]);

        Http::fake([
            route('order.externalApi') => Http::response(null, 500),
        ]);

        (new UpdateOrderStatus())->handle();
        $order->refresh();
        $this->assertEquals('pending', $order->status);
    }
}
