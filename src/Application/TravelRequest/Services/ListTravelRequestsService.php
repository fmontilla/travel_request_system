<?php

namespace Application\TravelRequest\Services;

use Application\TravelRequest\DTOs\TravelRequestFiltersDTO;
use Domain\TravelRequest\Repositories\TravelRequestRepositoryInterface;

class ListTravelRequestsService
{
    public function __construct(
        private TravelRequestRepositoryInterface $repository
    ) {
    }

    public function execute(TravelRequestFiltersDTO $filters): array
    {
        return $this->repository->findByFilters(
            $filters->userId,
            $filters->status,
            $filters->startDate,
            $filters->endDate,
            $filters->destination
        );
    }
}
