<?php

namespace App\Providers;

use Application\TravelRequest\Contracts\NotificationServiceInterface;
use Application\TravelRequest\Services\CreateTravelRequestService;
use Application\TravelRequest\Services\GetTravelRequestService;
use Application\TravelRequest\Services\ListTravelRequestsService;
use Application\TravelRequest\Services\UpdateTravelRequestStatusService;
use Application\User\Services\RegisterUserService;
use Domain\TravelRequest\Repositories\TravelRequestRepositoryInterface;
use Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Notifications\EmailNotificationService;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentTravelRequestRepository;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TravelRequestRepositoryInterface::class,
            EloquentTravelRequestRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            NotificationServiceInterface::class,
            EmailNotificationService::class
        );

        $this->app->singleton(CreateTravelRequestService::class);
        $this->app->singleton(GetTravelRequestService::class);
        $this->app->singleton(ListTravelRequestsService::class);
        $this->app->singleton(UpdateTravelRequestStatusService::class);
        $this->app->singleton(RegisterUserService::class);
    }

    public function boot(): void
    {
    }
}
