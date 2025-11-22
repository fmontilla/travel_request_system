<?php

namespace Application\TravelRequest\DTOs;

use DateTimeImmutable;

class CreateTravelRequestDTO
{
    public function __construct(
        public int $userId,
        public string $requesterName,
        public string $destination,
        public DateTimeImmutable $departureDate,
        public DateTimeImmutable $returnDate
    ) {
    }
}
