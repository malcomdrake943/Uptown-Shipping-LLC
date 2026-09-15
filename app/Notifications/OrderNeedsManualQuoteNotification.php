<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderNeedsManualQuoteNotification extends Notification implements ShouldQueue
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
            ->subject("Quote Needed – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("Thank you for submitting order **{$this->order->order_number}**.")
            ->line("Because you selected the **Oversized** size tier, our team needs to manually calculate shipping and handling costs before we can collect payment.")
            ->line("**Product:** " . ($this->order->product_name ?? $this->order->product_url))
            ->line("We'll review your request and send you a payment link within 1–2 business days.")
            ->line("You don't need to do anything right now.")
            ->salutation("Thank you for your patience – Jubilee Direct Team");
    }
}
