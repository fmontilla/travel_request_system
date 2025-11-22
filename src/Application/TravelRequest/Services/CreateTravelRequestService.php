<?php

namespace Application\TravelRequest\Services;

use Application\TravelRequest\DTOs\CreateTravelRequestDTO;
use Domain\TravelRequest\Entities\TravelRequest;
use Domain\TravelRequest\Repositories\TravelRequestRepositoryInterface;
use Domain\TravelRequest\ValueObjects\Destination;
use Domain\TravelRequest\ValueObjects\TravelPeriod;

class CreateTravelRequestService
{
    public function __construct(
        private TravelRequestRepositoryInterface $repository
    ) {
    }

    public function execute(CreateTravelRequestDTO $dto): TravelRequest
    {
        $destination = Destination::fromString($dto->destination);
        $travelPeriod = TravelPeriod::create($dto->departureDate, $dto->returnDate);

        $travelRequest = TravelRequest::create(
            $dto->userId,
            $dto->requesterName,
            $destination,
            $travelPeriod
        );

        $this->repository->save($travelRequest);

        return $travelRequest;
    }
}
