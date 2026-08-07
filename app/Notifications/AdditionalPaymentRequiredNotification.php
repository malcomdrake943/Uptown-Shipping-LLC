<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdditionalPaymentRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Order  $order,
        public readonly float  $amount,
        public readonly string $clientSecret,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $paymentUrl = route('order.additional-payment', [
            'order'  => $this->order->id,
            'secret' => $this->clientSecret,
        ]);

        return (new MailMessage)
            ->subject("Additional Payment Required – {$this->order->order_number}")
            ->greeting("Hello {$this->order->customer_name}!")
            ->line("The actual cost of your order **{$this->order->order_number}** was slightly higher than your initial estimate.")
            ->line("**Additional amount due:** $" . number_format($this->amount, 2))
            ->line("Please complete the additional payment to allow us to proceed with your order.")
            ->action('Pay Now – $' . number_format($this->amount, 2), $paymentUrl)
            ->line("You will not be charged without clicking the button above.")
            ->salutation("Thank you – Jubilee Nexus Group Team");
    }
}
