<?php

namespace Domain\TravelRequest\ValueObjects;

use InvalidArgumentException;

class Destination
{
    private function __construct(private string $value)
    {
        $this->validate($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(string $value): void
    {
        $trimmed = trim($value);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Destination cannot be empty');
        }

        if (strlen($trimmed) < 2) {
            throw new InvalidArgumentException('Destination must be at least 2 characters long');
        }

        if (strlen($trimmed) > 255) {
            throw new InvalidArgumentException('Destination cannot exceed 255 characters');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
