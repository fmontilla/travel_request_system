<?php

namespace Application\TravelRequest\DTOs;

use DateTimeImmutable;
use Domain\TravelRequest\Enums\TravelRequestStatus;

class TravelRequestFiltersDTO
{
    public function __construct(
        public int $userId,
        public ?TravelRequestStatus $status = null,
        public ?DateTimeImmutable $startDate = null,
        public ?DateTimeImmutable $endDate = null,
        public ?string $destination = null
    ) {
    }
}
