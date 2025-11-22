<?php

namespace Tests\Unit\Domain\TravelRequest\ValueObjects;

use Domain\TravelRequest\ValueObjects\Destination;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DestinationTest extends TestCase
{
    public function test_can_create_valid_destination(): void
    {
        $destination = Destination::fromString('New York');

        $this->assertEquals('New York', $destination->value());
    }

    public function test_cannot_create_empty_destination(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Destination cannot be empty');

        Destination::fromString('');
    }

    public function test_cannot_create_too_short_destination(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Destination must be at least 2 characters long');

        Destination::fromString('A');
    }

    public function test_cannot_create_too_long_destination(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Destination cannot exceed 255 characters');

        Destination::fromString(str_repeat('A', 256));
    }

    public function test_destination_equality(): void
    {
        $destination1 = Destination::fromString('Paris');
        $destination2 = Destination::fromString('Paris');
        $destination3 = Destination::fromString('London');

        $this->assertTrue($destination1->equals($destination2));
        $this->assertFalse($destination1->equals($destination3));
    }
}
