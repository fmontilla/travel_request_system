<?php

namespace Domain\TravelRequest\Events;

use DateTimeImmutable;

class TravelRequestCancelled
{
    public function __construct(
        public string $travelRequestId,
        public int $userId,
        public string $requesterName,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable()
    ) {
    }
}
