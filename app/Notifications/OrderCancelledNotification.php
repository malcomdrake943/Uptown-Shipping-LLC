<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order  $order,
        public readonly string $reason = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Order Cancelled – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("Your order **{$this->order->order_number}** has been cancelled.");

        if ($this->reason) {
            $mail->line("**Reason:** {$this->reason}");
        }

        $mail->line("If a refund is applicable, it will be processed to your original payment method within 5–10 business days.");

        return $mail->salutation("We apologize for any inconvenience – Jubilee Nexus Group Team");
    }
}
