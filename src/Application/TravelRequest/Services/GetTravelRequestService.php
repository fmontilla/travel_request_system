<?php

namespace Application\TravelRequest\Services;

use Domain\TravelRequest\Entities\TravelRequest;
use Domain\TravelRequest\Repositories\TravelRequestRepositoryInterface;
use Domain\Shared\ValueObjects\Uuid;
use InvalidArgumentException;

class GetTravelRequestService
{
    public function __construct(
        private TravelRequestRepositoryInterface $repository
    ) {
    }

    public function execute(string $id, int $userId): TravelRequest
    {
        $uuid = Uuid::fromString($id);
        $travelRequest = $this->repository->findByIdAndUserId($uuid, $userId);

        if (!$travelRequest) {
            throw new InvalidArgumentException('Travel request not found or access denied');
        }

        return $travelRequest;
    }
}
