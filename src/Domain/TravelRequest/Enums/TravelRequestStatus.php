<?php

namespace Domain\TravelRequest\Enums;

enum TravelRequestStatus: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';

    public function canBeUpdatedTo(self $newStatus): bool
    {
        return match ($this) {
            self::REQUESTED => in_array($newStatus, [self::APPROVED, self::CANCELLED]),
            self::APPROVED => false,
            self::CANCELLED => false,
        };
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isRequested(): bool
    {
        return $this === self::REQUESTED;
    }
}
