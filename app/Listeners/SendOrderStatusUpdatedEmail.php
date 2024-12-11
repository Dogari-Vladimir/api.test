<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusUpdatedEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;
        Mail::to('user@example.com')->send(new OrderStatusUpdatedMail($order));

    }
}
