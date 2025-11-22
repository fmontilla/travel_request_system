<?php

namespace App\Policies;

use Infrastructure\Persistence\Eloquent\Models\TravelRequestModel;
use Infrastructure\Persistence\Eloquent\Models\User;

class TravelRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelRequestModel $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TravelRequestModel $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id;
    }

    public function delete(User $user, TravelRequestModel $travelRequest): bool
    {
        return $user->id === $travelRequest->user_id;
    }

    public function updateStatus(User $user): bool
    {
        return $user->isAdmin();
    }
}


