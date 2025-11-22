<?php

namespace Infrastructure\Persistence\Eloquent\Repositories;

use DateTimeImmutable;
use Domain\TravelRequest\Entities\TravelRequest;
use Domain\TravelRequest\Enums\TravelRequestStatus;
use Domain\TravelRequest\Repositories\TravelRequestRepositoryInterface;
use Domain\TravelRequest\ValueObjects\Destination;
use Domain\TravelRequest\ValueObjects\TravelPeriod;
use Domain\Shared\ValueObjects\Uuid;
use Infrastructure\Persistence\Eloquent\Models\TravelRequestModel;

class EloquentTravelRequestRepository implements TravelRequestRepositoryInterface
{
    public function save(TravelRequest $travelRequest): void
    {
        TravelRequestModel::query()->updateOrCreate(
            ['id' => $travelRequest->id()->value()],
            [
                'user_id' => $travelRequest->userId(),
                'requester_name' => $travelRequest->requesterName(),
                'destination' => $travelRequest->destination()->value(),
                'departure_date' => $travelRequest->travelPeriod()->departureDate(),
                'return_date' => $travelRequest->travelPeriod()->returnDate(),
                'status' => $travelRequest->status()->value,
                'updated_at' => $travelRequest->updatedAt(),
            ]
        );
    }

    public function findById(Uuid $id): ?TravelRequest
    {
        $model = TravelRequestModel::query()->find($id->value());

        return $model ? $this->toDomain($model) : null;
    }

    public function findByIdAndUserId(Uuid $id, int $userId): ?TravelRequest
    {
        $model = TravelRequestModel::query()
            ->where('id', $id->value())
            ->where('user_id', $userId)
            ->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findAllByUserId(int $userId): array
    {
        $models = TravelRequestModel::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomain($model))->all();
    }

    public function findByFilters(
        int $userId,
        ?TravelRequestStatus $status = null,
        ?DateTimeImmutable $startDate = null,
        ?DateTimeImmutable $endDate = null,
        ?string $destination = null
    ): array {
        $query = TravelRequestModel::query()->where('user_id', $userId);

        if ($status) {
            $query->where('status', $status->value);
        }

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('departure_date', [$startDate, $endDate])
                    ->orWhereBetween('return_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('departure_date', '<=', $startDate)
                            ->where('return_date', '>=', $endDate);
                    });
            });
        }

        if ($destination) {
            $query->where('destination', 'like', "%{$destination}%");
        }

        $models = $query->orderBy('created_at', 'desc')->get();

        return $models->map(fn($model) => $this->toDomain($model))->all();
    }

    public function delete(TravelRequest $travelRequest): void
    {
        TravelRequestModel::query()
            ->where('id', $travelRequest->id()->value())
            ->delete();
    }

    private function toDomain(TravelRequestModel $model): TravelRequest
    {
        return TravelRequest::reconstitute(
            Uuid::fromString($model->id),
            $model->user_id,
            $model->requester_name,
            Destination::fromString($model->destination),
            TravelPeriod::create(
                DateTimeImmutable::createFromMutable($model->departure_date),
                DateTimeImmutable::createFromMutable($model->return_date)
            ),
            TravelRequestStatus::from($model->status),
            DateTimeImmutable::createFromMutable($model->created_at),
            DateTimeImmutable::createFromMutable($model->updated_at)
        );
    }
}
