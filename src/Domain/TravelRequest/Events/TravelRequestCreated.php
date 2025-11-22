<?php

namespace Domain\TravelRequest\Events;

use DateTimeImmutable;

class TravelRequestCreated
{
    public function __construct(
        public string $travelRequestId,
        public int $userId,
        public string $requesterName,
        public string $destination,
        public DateTimeImmutable $departureDate,
        public DateTimeImmutable $returnDate,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable()
    ) {
    }
}
