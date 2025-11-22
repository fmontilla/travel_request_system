<?php

namespace Application\TravelRequest\Services;

use Application\TravelRequest\Contracts\NotificationServiceInterface;
use Application\TravelRequest\DTOs\UpdateTravelRequestStatusDTO;
use Domain\TravelRequest\Entities\TravelRequest;
use Domain\TravelRequest\Enums\TravelRequestStatus;
use Domain\TravelRequest\Repositories\TravelRequestRepositoryInterface;
use Domain\Shared\ValueObjects\Uuid;
use InvalidArgumentException;

class UpdateTravelRequestStatusService
{
    public function __construct(
        private TravelRequestRepositoryInterface $repository,
        private NotificationServiceInterface $notificationService
    ) {
    }

    public function execute(UpdateTravelRequestStatusDTO $dto): TravelRequest
    {
        $uuid = Uuid::fromString($dto->travelRequestId);
        $travelRequest = $this->repository->findById($uuid);

        if (!$travelRequest) {
            throw new InvalidArgumentException('Travel request not found');
        }

        $this->updateStatus($travelRequest, $dto->newStatus);
        $this->repository->save($travelRequest);

        $this->sendNotification($travelRequest, $dto->newStatus);

        return $travelRequest;
    }

    private function updateStatus(TravelRequest $travelRequest, TravelRequestStatus $newStatus): void
    {
        match ($newStatus) {
            TravelRequestStatus::APPROVED => $travelRequest->approve(),
            TravelRequestStatus::CANCELLED => $travelRequest->cancel(),
            default => throw new InvalidArgumentException('Invalid status transition'),
        };
    }

    private function sendNotification(TravelRequest $travelRequest, TravelRequestStatus $status): void
    {
        match ($status) {
            TravelRequestStatus::APPROVED => $this->notificationService->notifyTravelRequestApproved($travelRequest),
            TravelRequestStatus::CANCELLED => $this->notificationService->notifyTravelRequestCancelled($travelRequest),
            default => null,
        };
    }
}
