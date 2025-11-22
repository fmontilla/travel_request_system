<?php

namespace Infrastructure\Notifications\Notifications;

use Domain\TravelRequest\Entities\TravelRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelRequestCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly TravelRequest $travelRequest
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Travel Request Cancelled')
            ->greeting("Hello {$this->travelRequest->requesterName()},")
            ->line('Your travel request has been cancelled.')
            ->line("Destination: {$this->travelRequest->destination()->value()}")
            ->line("Departure: {$this->travelRequest->travelPeriod()->departureDate()->format('Y-m-d')}")
            ->line("Return: {$this->travelRequest->travelPeriod()->returnDate()->format('Y-m-d')}")
            ->line('If you have any questions, please contact support.');
    }
}
