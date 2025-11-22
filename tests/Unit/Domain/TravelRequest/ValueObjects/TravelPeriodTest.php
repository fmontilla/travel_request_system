<?php

namespace Tests\Unit\Domain\TravelRequest\ValueObjects;

use DateTimeImmutable;
use Domain\TravelRequest\ValueObjects\TravelPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TravelPeriodTest extends TestCase
{
    public function test_can_create_valid_travel_period(): void
    {
        $departureDate = new DateTimeImmutable('2025-02-01');
        $returnDate = new DateTimeImmutable('2025-02-10');

        $period = TravelPeriod::create($departureDate, $returnDate);

        $this->assertEquals($departureDate, $period->departureDate());
        $this->assertEquals($returnDate, $period->returnDate());
    }

    public function test_cannot_create_period_with_return_before_departure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Return date must be after departure date');

        $departureDate = new DateTimeImmutable('2025-02-10');
        $returnDate = new DateTimeImmutable('2025-02-01');

        TravelPeriod::create($departureDate, $returnDate);
    }

    public function test_cannot_create_period_with_same_dates(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Return date must be different from departure date');

        $date = new DateTimeImmutable('2025-02-01');

        TravelPeriod::create($date, $date);
    }

    public function test_calculates_duration_in_days(): void
    {
        $departureDate = new DateTimeImmutable('2025-02-01');
        $returnDate = new DateTimeImmutable('2025-02-10');

        $period = TravelPeriod::create($departureDate, $returnDate);

        $this->assertEquals(9, $period->durationInDays());
    }

    public function test_checks_if_within_period(): void
    {
        $departureDate = new DateTimeImmutable('2025-02-01');
        $returnDate = new DateTimeImmutable('2025-02-10');
        $period = TravelPeriod::create($departureDate, $returnDate);

        $startDate = new DateTimeImmutable('2025-01-01');
        $endDate = new DateTimeImmutable('2025-03-01');

        $this->assertTrue($period->isWithinPeriod($startDate, $endDate));
    }

    public function test_checks_if_overlaps_with_period(): void
    {
        $departureDate = new DateTimeImmutable('2025-02-01');
        $returnDate = new DateTimeImmutable('2025-02-10');
        $period = TravelPeriod::create($departureDate, $returnDate);

        $startDate = new DateTimeImmutable('2025-02-05');
        $endDate = new DateTimeImmutable('2025-02-15');

        $this->assertTrue($period->overlapsWithPeriod($startDate, $endDate));
    }
}
