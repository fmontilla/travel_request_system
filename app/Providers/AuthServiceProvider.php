<?php

namespace App\Providers;

use App\Policies\TravelRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Infrastructure\Persistence\Eloquent\Models\TravelRequestModel;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        TravelRequestModel::class => TravelRequestPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

