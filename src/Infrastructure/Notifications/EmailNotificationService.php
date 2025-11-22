<?php

namespace Infrastructure\Notifications;

use Application\TravelRequest\Contracts\NotificationServiceInterface;
use Domain\TravelRequest\Entities\TravelRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Infrastructure\Notifications\Notifications\TravelRequestApprovedNotification;
use Infrastructure\Notifications\Notifications\TravelRequestCancelledNotification;
use Infrastructure\Persistence\Eloquent\Models\User;

class EmailNotificationService implements NotificationServiceInterface
{
    public function notifyTravelRequestApproved(TravelRequest $travelRequest): void
    {
        $user = User::find($travelRequest->userId());

        if (!$user) {
            Log::warning('User not found for notification', [
                'user_id' => $travelRequest->userId(),
                'travel_request_id' => $travelRequest->id()->value(),
            ]);
            return;
        }

        Notification::send($user, new TravelRequestApprovedNotification($travelRequest));
    }

    public function notifyTravelRequestCancelled(TravelRequest $travelRequest): void
    {
        $user = User::find($travelRequest->userId());

        if (!$user) {
            Log::warning('User not found for notification', [
                'user_id' => $travelRequest->userId(),
                'travel_request_id' => $travelRequest->id()->value(),
            ]);
            return;
        }

        Notification::send($user, new TravelRequestCancelledNotification($travelRequest));
    }
}
