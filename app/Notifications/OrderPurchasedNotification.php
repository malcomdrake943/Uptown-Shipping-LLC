<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPurchasedNotification extends Notification implements ShouldQueue
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
            ->subject("Item Purchased – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("We've successfully purchased your item for order **{$this->order->order_number}**!")
            ->line("**Product:** " . ($this->order->product_name ?? $this->order->product_url))
            ->line("We'll notify you again as soon as the item has shipped with tracking information.")
            ->salutation("Thank you – Jubilee Direct Team");
    }
}
