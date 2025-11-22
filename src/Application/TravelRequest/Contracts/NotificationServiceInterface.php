<?php

namespace Application\TravelRequest\Contracts;

use Domain\TravelRequest\Entities\TravelRequest;

interface NotificationServiceInterface
{
    public function notifyTravelRequestApproved(TravelRequest $travelRequest): void;

    public function notifyTravelRequestCancelled(TravelRequest $travelRequest): void;
}
