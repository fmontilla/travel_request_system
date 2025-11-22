<?php

namespace Domain\TravelRequest\Repositories;

use DateTimeImmutable;
use Domain\TravelRequest\Entities\TravelRequest;
use Domain\TravelRequest\Enums\TravelRequestStatus;
use Domain\Shared\ValueObjects\Uuid;

interface TravelRequestRepositoryInterface
{
    public function save(TravelRequest $travelRequest): void;

    public function findById(Uuid $id): ?TravelRequest;

    public function findByIdAndUserId(Uuid $id, int $userId): ?TravelRequest;

    public function findAllByUserId(int $userId): array;

    public function findByFilters(
        int $userId,
        ?TravelRequestStatus $status = null,
        ?DateTimeImmutable $startDate = null,
        ?DateTimeImmutable $endDate = null,
        ?string $destination = null
    ): array;

    public function delete(TravelRequest $travelRequest): void;
}
