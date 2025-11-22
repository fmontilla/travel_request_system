<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Infrastructure\Persistence\Eloquent\Models\User;

class SendTravelRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        protected int $userId,
        protected string $travelRequestId,
        protected string $status,
        protected string $notificationType
    ) {
    }

    public function handle(): void
    {
        try {
            $user = User::find($this->userId);

            if (!$user) {
                Log::warning("User not found for notification: {$this->userId}");
                return;
            }

            Log::info("Notification sent", [
                'user_id' => $this->userId,
                'user_email' => $user->email,
                'user_name' => $user->name,
                'travel_request_id' => $this->travelRequestId,
                'status' => $this->status,
                'notification_type' => $this->notificationType,
                'timestamp' => Carbon::now()->toIso8601String(),
            ]);

            // In production, send actual notification:
            // $user->notify(new TravelRequestStatusChanged($this->travelRequestId, $this->status));
            // Or use Laravel Mail:
            // Mail::to($user->email)->send(new TravelRequestNotificationMail(...));

        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage(), [
                'user_id' => $this->userId,
                'travel_request_id' => $this->travelRequestId,
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Notification job failed after all retries", [
            'user_id' => $this->userId,
            'travel_request_id' => $this->travelRequestId,
            'error' => $exception->getMessage(),
            'stack_trace' => $exception->getTraceAsString(),
        ]);
    }
}


