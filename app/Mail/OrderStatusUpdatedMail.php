<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build(): OrderStatusUpdatedMail
    {
        return $this->view('emails.order_status_updated')
            ->subject('Обновление статуса вашего заказа')
            ->with([
                'orderNumber' => $this->order->order_number,
                'status' => $this->order->status,
            ]);
    }
}
