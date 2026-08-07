<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OrderConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject("Order Confirmed – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("Your order **{$this->order->order_number}** has been confirmed and payment received.")
            ->line("We'll now review your order and purchase the item on your behalf.")
            ->line("**Product:** " . ($this->order->product_name ?? $this->order->product_url))
            ->line("**Total Charged:** $" . number_format($this->order->total_charged, 2))
            ->action('Track Your Order', $trackingUrl)
            ->line("This tracking link is valid for 90 days. Keep it safe!")
            ->salutation("Thank you for using Jubilee Nexus Group!");
    }
}
