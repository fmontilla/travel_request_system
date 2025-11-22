<?php

namespace Tests\Unit\Domain\TravelRequest;

use DateTimeImmutable;
use Domain\TravelRequest\Entities\TravelRequest;
use Domain\TravelRequest\Enums\TravelRequestStatus;
use Domain\TravelRequest\ValueObjects\Destination;
use Domain\TravelRequest\ValueObjects\TravelPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TravelRequestTest extends TestCase
{
    public function test_can_create_travel_request(): void
    {
        $destination = Destination::fromString('New York');
        $travelPeriod = TravelPeriod::create(
            new DateTimeImmutable('2025-02-01'),
            new DateTimeImmutable('2025-02-10')
        );

        $travelRequest = TravelRequest::create(
            1,
            'John Doe',
            $destination,
            $travelPeriod
        );

        $this->assertInstanceOf(TravelRequest::class, $travelRequest);
        $this->assertEquals(1, $travelRequest->userId());
        $this->assertEquals('John Doe', $travelRequest->requesterName());
        $this->assertEquals('New York', $travelRequest->destination()->value());
        $this->assertEquals(TravelRequestStatus::REQUESTED, $travelRequest->status());
    }

    public function test_can_approve_travel_request(): void
    {
        $destination = Destination::fromString('Paris');
        $travelPeriod = TravelPeriod::create(
            new DateTimeImmutable('2025-02-01'),
            new DateTimeImmutable('2025-02-10')
        );

        $travelRequest = TravelRequest::create(
            1,
            'Jane Smith',
            $destination,
            $travelPeriod
        );

        $travelRequest->approve();

        $this->assertEquals(TravelRequestStatus::APPROVED, $travelRequest->status());
    }

    public function test_can_cancel_requested_travel_request(): void
    {
        $destination = Destination::fromString('London');
        $travelPeriod = TravelPeriod::create(
            new DateTimeImmutable('2025-02-01'),
            new DateTimeImmutable('2025-02-10')
        );

        $travelRequest = TravelRequest::create(
            1,
            'Bob Johnson',
            $destination,
            $travelPeriod
        );

        $travelRequest->cancel();

        $this->assertEquals(TravelRequestStatus::CANCELLED, $travelRequest->status());
    }

    public function test_cannot_cancel_approved_travel_request(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot cancel an already approved travel request');

        $destination = Destination::fromString('Tokyo');
        $travelPeriod = TravelPeriod::create(
            new DateTimeImmutable('2025-02-01'),
            new DateTimeImmutable('2025-02-10')
        );

        $travelRequest = TravelRequest::create(
            1,
            'Alice Brown',
            $destination,
            $travelPeriod
        );

        $travelRequest->approve();
        $travelRequest->cancel();
    }

    public function test_cannot_change_status_from_approved(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $destination = Destination::fromString('Berlin');
        $travelPeriod = TravelPeriod::create(
            new DateTimeImmutable('2025-02-01'),
            new DateTimeImmutable('2025-02-10')
        );

        $travelRequest = TravelRequest::create(
            1,
            'Charlie Davis',
            $destination,
            $travelPeriod
        );

        $travelRequest->approve();
        $travelRequest->approve();
    }

    public function test_is_owned_by_user(): void
    {
        $destination = Destination::fromString('Rome');
        $travelPeriod = TravelPeriod::create(
            new DateTimeImmutable('2025-02-01'),
            new DateTimeImmutable('2025-02-10')
        );

        $travelRequest = TravelRequest::create(
            1,
            'Dave Wilson',
            $destination,
            $travelPeriod
        );

        $this->assertTrue($travelRequest->isOwnedBy(1));
        $this->assertFalse($travelRequest->isOwnedBy(2));
    }
}
