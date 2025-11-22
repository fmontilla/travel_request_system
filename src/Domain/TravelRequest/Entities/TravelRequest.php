<?php

namespace Domain\TravelRequest\Entities;

use DateTimeImmutable;
use Domain\TravelRequest\Enums\TravelRequestStatus;
use Domain\TravelRequest\Events\TravelRequestApproved;
use Domain\TravelRequest\Events\TravelRequestCancelled;
use Domain\TravelRequest\Events\TravelRequestCreated;
use Domain\TravelRequest\ValueObjects\Destination;
use Domain\TravelRequest\ValueObjects\TravelPeriod;
use Domain\Shared\ValueObjects\Uuid;
use InvalidArgumentException;

class TravelRequest
{
    private array $domainEvents = [];

    private function __construct(
        private Uuid $id,
        private int $userId,
        private string $requesterName,
        private Destination $destination,
        private TravelPeriod $travelPeriod,
        private TravelRequestStatus $status,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt
    ) {
    }

    public static function create(
        int $userId,
        string $requesterName,
        Destination $destination,
        TravelPeriod $travelPeriod
    ): self {
        $travelRequest = new self(
            Uuid::generate(),
            $userId,
            $requesterName,
            $destination,
            $travelPeriod,
            TravelRequestStatus::REQUESTED,
            new DateTimeImmutable(),
            new DateTimeImmutable()
        );

        $travelRequest->recordEvent(new TravelRequestCreated(
            $travelRequest->id->value(),
            $userId,
            $requesterName,
            $destination->value(),
            $travelPeriod->departureDate(),
            $travelPeriod->returnDate()
        ));

        return $travelRequest;
    }

    public static function reconstitute(
        Uuid $id,
        int $userId,
        string $requesterName,
        Destination $destination,
        TravelPeriod $travelPeriod,
        TravelRequestStatus $status,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $id,
            $userId,
            $requesterName,
            $destination,
            $travelPeriod,
            $status,
            $createdAt,
            $updatedAt
        );
    }

    public function approve(): void
    {
        $this->changeStatus(TravelRequestStatus::APPROVED);

        $this->recordEvent(new TravelRequestApproved(
            $this->id->value(),
            $this->userId,
            $this->requesterName
        ));
    }

    public function cancel(): void
    {
        if ($this->status->isApproved()) {
            throw new InvalidArgumentException('Cannot cancel an already approved travel request');
        }

        $this->changeStatus(TravelRequestStatus::CANCELLED);

        $this->recordEvent(new TravelRequestCancelled(
            $this->id->value(),
            $this->userId,
            $this->requesterName
        ));
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->userId === $userId;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function requesterName(): string
    {
        return $this->requesterName;
    }

    public function destination(): Destination
    {
        return $this->destination;
    }

    public function travelPeriod(): TravelPeriod
    {
        return $this->travelPeriod;
    }

    public function status(): TravelRequestStatus
    {
        return $this->status;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    private function changeStatus(TravelRequestStatus $newStatus): void
    {
        if (!$this->status->canBeUpdatedTo($newStatus)) {
            throw new InvalidArgumentException(
                "Cannot change status from {$this->status->value} to {$newStatus->value}"
            );
        }

        $this->status = $newStatus;
        $this->updatedAt = new DateTimeImmutable();
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
