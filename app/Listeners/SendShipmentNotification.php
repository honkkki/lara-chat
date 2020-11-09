<?php

namespace App\Listeners;

use App\Events\Order\OrderShipped;
use App\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendShipmentNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderShipped  $event
     * @return void
     */
    public function handle(OrderShipped $event)
    {
        $content = $event->content;
        $mail = new Mail();
        $mail['content'] = $content;
        $mail->save();

    }
}
