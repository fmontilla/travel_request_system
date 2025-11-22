<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Domain\TravelRequest\Entities\TravelRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $travelRequest = $this->resource;

        if (!$travelRequest instanceof TravelRequest) {
            return [];
        }

        $departureDate = $travelRequest->travelPeriod()->departureDate();
        $returnDate = $travelRequest->travelPeriod()->returnDate();

        return [
            'id' => $travelRequest->id()->value(),
            'user_id' => $travelRequest->userId(),
            'requester_name' => $travelRequest->requesterName(),
            'destination' => $travelRequest->destination()->value(),
            'departure_date' => $departureDate->format('Y-m-d H:i:s'),
            'return_date' => $returnDate->format('Y-m-d H:i:s'),
            'status' => $travelRequest->status()->value,
            'created_at' => $travelRequest->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $travelRequest->updatedAt()->format('Y-m-d H:i:s'),

            'duration_days' => $this->when(
                true,
                $travelRequest->travelPeriod()->durationInDays()
            ),

            'status_label' => $this->when(
                true,
                $this->getStatusLabel($travelRequest->status()->value)
            ),

            'is_upcoming' => $this->when(
                true,
                $departureDate > Carbon::now()->toDateTimeImmutable()
            ),
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => Carbon::now()->toIso8601String(),
            ],
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'requested' => 'Pending Approval',
            'approved' => 'Approved',
            'cancelled' => 'Cancelled',
            default => ucfirst($status),
        };
    }
}
