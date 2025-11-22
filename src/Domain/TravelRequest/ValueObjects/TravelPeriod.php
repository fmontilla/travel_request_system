<?php

namespace Domain\TravelRequest\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

class TravelPeriod
{
    private function __construct(
        private DateTimeImmutable $departureDate,
        private DateTimeImmutable $returnDate
    ) {
        $this->validate();
    }

    public static function create(DateTimeImmutable $departureDate, DateTimeImmutable $returnDate): self
    {
        return new self($departureDate, $returnDate);
    }

    public function departureDate(): DateTimeImmutable
    {
        return $this->departureDate;
    }

    public function returnDate(): DateTimeImmutable
    {
        return $this->returnDate;
    }

    public function durationInDays(): int
    {
        return $this->departureDate->diff($this->returnDate)->days;
    }

    public function isWithinPeriod(DateTimeImmutable $startDate, DateTimeImmutable $endDate): bool
    {
        return $this->departureDate >= $startDate && $this->returnDate <= $endDate;
    }

    public function overlapsWithPeriod(DateTimeImmutable $startDate, DateTimeImmutable $endDate): bool
    {
        return $this->departureDate <= $endDate && $this->returnDate >= $startDate;
    }

    private function validate(): void
    {
        if ($this->returnDate < $this->departureDate) {
            throw new InvalidArgumentException('Return date must be after departure date');
        }

        if ($this->returnDate == $this->departureDate) {
            throw new InvalidArgumentException('Return date must be different from departure date');
        }
    }

    public function equals(self $other): bool
    {
        return $this->departureDate == $other->departureDate
            && $this->returnDate == $other->returnDate;
    }
}
