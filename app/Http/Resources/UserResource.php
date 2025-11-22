<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            'travel_requests_count' => $this->whenLoaded('travelRequests', function () {
                return $this->travelRequests->count();
            }),

            'email_verified_at' => $this->when(
                $request->user()?->id === $this->id,
                $this->email_verified_at?->format('Y-m-d H:i:s')
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
}

