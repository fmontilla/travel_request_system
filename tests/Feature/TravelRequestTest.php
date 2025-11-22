<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Infrastructure\Persistence\Eloquent\Models\User;
use Tests\TestCase;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private string $userToken;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $this->userToken = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::fromUser($this->user);
        $this->adminToken = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::fromUser($this->admin);
    }

    public function test_user_can_create_travel_request(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'New York',
                'departure_date' => '2026-02-01',
                'return_date' => '2026-02-10',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'requester_name',
                    'destination',
                    'departure_date',
                    'return_date',
                    'status',
                    'created_at',
                    'updated_at',
                ]
            ])
            ->assertJson([
                'data' => [
                    'user_id' => $this->user->id,
                    'requester_name' => 'Regular User',
                    'destination' => 'New York',
                    'status' => 'requested',
                ]
            ]);
    }

    public function test_user_can_view_their_own_travel_request(): void
    {
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Paris',
                'departure_date' => '2026-03-01',
                'return_date' => '2026-03-10',
            ]);

        $travelRequestId = $createResponse->json('data.id');

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->getJson("/api/v1/travel-requests/{$travelRequestId}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $travelRequestId,
                    'user_id' => $this->user->id,
                    'destination' => 'Paris',
                ]
            ]);
    }

    public function test_user_can_list_their_travel_requests(): void
    {
        $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'London',
                'departure_date' => '2026-04-01',
                'return_date' => '2026-04-10',
            ]);

        $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Tokyo',
                'departure_date' => '2026-05-01',
                'return_date' => '2026-05-10',
            ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->getJson('/api/v1/travel-requests');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_filter_travel_requests_by_status(): void
    {
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Berlin',
                'departure_date' => '2026-06-01',
                'return_date' => '2026-06-10',
            ]);

        $travelRequestId = $createResponse->json('data.id');

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/travel-requests/{$travelRequestId}/status", [
                'status' => 'approved',
            ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->getJson('/api/v1/travel-requests?status=approved');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    [
                        'status' => 'approved',
                    ]
                ]
            ]);
    }

    public function test_admin_can_approve_travel_request(): void
    {
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Rome',
                'departure_date' => '2026-07-01',
                'return_date' => '2026-07-10',
            ]);

        $travelRequestId = $createResponse->json('data.id');

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/travel-requests/{$travelRequestId}/status", [
                'status' => 'approved',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'approved',
                ]
            ]);
    }

    public function test_admin_can_cancel_requested_travel_request(): void
    {
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Madrid',
                'departure_date' => '2026-08-01',
                'return_date' => '2026-08-10',
            ]);

        $travelRequestId = $createResponse->json('data.id');

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/travel-requests/{$travelRequestId}/status", [
                'status' => 'cancelled',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'cancelled',
                ]
            ]);
    }

    public function test_regular_user_cannot_update_travel_request_status(): void
    {
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Amsterdam',
                'departure_date' => '2026-09-01',
                'return_date' => '2026-09-10',
            ]);

        $travelRequestId = $createResponse->json('data.id');

        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->patchJson("/api/v1/travel-requests/{$travelRequestId}/status", [
                'status' => 'approved',
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_cancel_approved_travel_request(): void
    {
        $createResponse = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'Regular User',
                'destination' => 'Barcelona',
                'departure_date' => '2026-10-01',
                'return_date' => '2026-10-10',
            ]);

        $travelRequestId = $createResponse->json('data.id');

        $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/travel-requests/{$travelRequestId}/status", [
                'status' => 'approved',
            ]);

        $response = $this->withHeader('Authorization', "Bearer {$this->adminToken}")
            ->patchJson("/api/v1/travel-requests/{$travelRequestId}/status", [
                'status' => 'cancelled',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot cancel an already approved travel request'
            ]);
    }

    public function test_validation_fails_with_invalid_data(): void
    {
        $response = $this->withHeader('Authorization', "Bearer {$this->userToken}")
            ->postJson('/api/v1/travel-requests', [
                'requester_name' => 'A',
                'destination' => 'X',
                'departure_date' => '2020-01-01',
                'return_date' => '2020-01-01',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['requester_name', 'destination', 'departure_date', 'return_date']);
    }
}
