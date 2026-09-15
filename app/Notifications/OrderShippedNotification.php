<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $trackingUrl = URL::temporarySignedRoute(
            'order.track',
            now()->addDays(90),
            ['order' => $this->order->id]
        );

        return (new MailMessage)
            ->subject("Your Order Has Shipped – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("Your order **{$this->order->order_number}** is on its way!")
            ->line("**Carrier:** " . ($this->order->tracking_carrier ?? 'See tracking page'))
            ->line("**Tracking Number:** " . ($this->order->tracking_number ?? 'See tracking page'))
            ->action('Track Your Shipment', $trackingUrl)
            ->line("Use the link above to view your order status and tracking information.")
            ->salutation("Thank you – Jubilee Direct Team");
    }
}
