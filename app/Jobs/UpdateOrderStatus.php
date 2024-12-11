<?php

namespace App\Jobs;

use App\Models\Order;
use \App\Events\OrderStatusUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // В зависимости от ответа внешнего API, будет строиться логика обновления статусов,
    // если API отправляет массив заказов с измененными статусами, тогда логика будет другрй
    // Циклом пройдемся по массиву и изменим статусы у заказов
    // В данном случаем я выбрал логику, собрать заказы с не конечными статусами и проверяю их по API

    public function handle()
    {
        $orders = Order::whereIn('status', ['pending', 'shipped'])->get();

        foreach ($orders as $order) {
            try {
                // Обращаюсь к внутреннему API, при внешнем будем стучаться по ссылке
                $response = Http::get(route('order.externalApi'));

//                $response = Http::get('https://external-api.com/order-status', [
//                    'order_number' => $order->order_number,
//                ]);

                if ($response->json()) {
                    $data = $response->json();

                    if (isset($data['status']) && $data['status'] !== $order->status) {
                        $order->update(['status' => $data['status']]);
                        event(new OrderStatusUpdated($order));
                    }
                } else {
                    Log::warning("Ошибка HTTP для заказа {$order->order_number}: {$response->body()}");
                }
            } catch (\Throwable $e) {
                Log::error("Ошибка при обновлении статуса заказа {$order->order_number}: {$e->getMessage()}");
            }
        }
    }
}
