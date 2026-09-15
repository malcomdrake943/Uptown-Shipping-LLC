<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Delivered – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("Your order **{$this->order->order_number}** has been marked as delivered!")
            ->line("We hope you love your purchase. If you have any questions or concerns, please reply to this email.")
            ->salutation("Thank you for choosing Jubilee Direct!");
    }
}
