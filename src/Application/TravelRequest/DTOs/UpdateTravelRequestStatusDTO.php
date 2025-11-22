<?php

namespace Application\TravelRequest\DTOs;

use Domain\TravelRequest\Enums\TravelRequestStatus;

class UpdateTravelRequestStatusDTO
{
    public function __construct(
        public string $travelRequestId,
        public int $adminUserId,
        public TravelRequestStatus $newStatus
    ) {
    }
}
