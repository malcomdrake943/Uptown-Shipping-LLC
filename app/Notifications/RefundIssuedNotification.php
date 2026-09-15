<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order $order,
        public readonly float $amount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Refund Issued – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("Great news! We've issued a partial refund for your order **{$this->order->order_number}**.")
            ->line("**Refund amount:** $" . number_format($this->amount, 2))
            ->line("The refund has been processed to your original payment method and typically appears within 5–10 business days, depending on your bank.")
            ->salutation("Thank you – Jubilee Direct Team");
    }
}
