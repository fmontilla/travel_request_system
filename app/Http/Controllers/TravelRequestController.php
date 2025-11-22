<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTravelRequestRequest;
use App\Http\Requests\UpdateTravelRequestStatusRequest;
use App\Http\Resources\TravelRequestResource;
use Application\TravelRequest\DTOs\CreateTravelRequestDTO;
use Application\TravelRequest\DTOs\TravelRequestFiltersDTO;
use Application\TravelRequest\DTOs\UpdateTravelRequestStatusDTO;
use Application\TravelRequest\Services\CreateTravelRequestService;
use Application\TravelRequest\Services\GetTravelRequestService;
use Application\TravelRequest\Services\ListTravelRequestsService;
use Application\TravelRequest\Services\UpdateTravelRequestStatusService;
use Carbon\Carbon;
use Domain\TravelRequest\Enums\TravelRequestStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class TravelRequestController extends Controller
{
    public function __construct(
        private readonly CreateTravelRequestService $createService,
        private readonly GetTravelRequestService $getService,
        private readonly ListTravelRequestsService $listService,
        private readonly UpdateTravelRequestStatusService $updateStatusService
    ) {
    }

    private function getAuthenticatedUser(Request $request)
    {
        if (app()->environment('testing')) {
            $token = $request->bearerToken();
            if ($token) {
                \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::setToken($token);
                Auth::guard('api')->setUser(\PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::authenticate());
            }
        }

        return Auth::guard('api')->user();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/travel-requests",
     *     tags={"Travel Requests"},
     *     summary="Create a new travel request",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"requester_name","destination","departure_date","return_date"},
     *             @OA\Property(property="requester_name", type="string", example="John Doe"),
     *             @OA\Property(property="destination", type="string", example="New York"),
     *             @OA\Property(property="departure_date", type="string", format="date", example="2025-02-01"),
     *             @OA\Property(property="return_date", type="string", format="date", example="2025-02-10")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Travel request created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(CreateTravelRequestRequest $request): JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            $dto = new CreateTravelRequestDTO(
                $user->id,
                $request->requester_name,
                $request->destination,
                Carbon::parse($request->departure_date)->toDateTimeImmutable(),
                Carbon::parse($request->return_date)->toDateTimeImmutable()
            );

            $travelRequest = $this->createService->execute($dto);

            return response()->json([
                'message' => 'Travel request created successfully',
                'data' => new TravelRequestResource($travelRequest)
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Travel request creation error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to create travel request. Please try again.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/travel-requests/{id}",
     *     tags={"Travel Requests"},
     *     summary="Get a specific travel request",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(response=200, description="Travel request details"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Travel request not found")
     * )
     */
    public function show(string $id, Request $request): JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser($request);
            $travelRequest = $this->getService->execute($id, $user->id);

            return response()->json([
                'data' => new TravelRequestResource($travelRequest)
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Travel request not found or access denied.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Travel request retrieval error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to retrieve travel request.'
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/travel-requests",
     *     tags={"Travel Requests"},
     *     summary="List all travel requests for authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"requested","approved","cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="destination",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="List of travel requests"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            if ($request->status && !in_array($request->status, ['requested', 'approved', 'cancelled'])) {
                return response()->json([
                    'message' => 'Invalid status. Must be one of: requested, approved, cancelled.'
                ], 422);
            }

            if ($request->start_date && !strtotime($request->start_date)) {
                return response()->json([
                    'message' => 'Invalid start date format. Use YYYY-MM-DD.'
                ], 422);
            }

            if ($request->end_date && !strtotime($request->end_date)) {
                return response()->json([
                    'message' => 'Invalid end date format. Use YYYY-MM-DD.'
                ], 422);
            }

            $filters = new TravelRequestFiltersDTO(
                $user->id,
                $request->status ? TravelRequestStatus::from($request->status) : null,
                $request->start_date ? Carbon::parse($request->start_date)->toDateTimeImmutable() : null,
                $request->end_date ? Carbon::parse($request->end_date)->toDateTimeImmutable() : null,
                $request->destination
            );

            $travelRequests = $this->listService->execute($filters);

            return TravelRequestResource::collection($travelRequests);
        } catch (\Exception $e) {
            Log::error('Travel request list error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to retrieve travel requests.'
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/travel-requests/{id}/status",
     *     tags={"Travel Requests"},
     *     summary="Update travel request status (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"approved","cancelled"}, example="approved")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Admin only"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateStatus(string $id, UpdateTravelRequestStatusRequest $request): JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            if (!$user->isAdmin()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }

            $dto = new UpdateTravelRequestStatusDTO(
                $id,
                $user->id,
                TravelRequestStatus::from($request->status)
            );

            $travelRequest = $this->updateStatusService->execute($dto);

            return response()->json([
                'message' => 'Travel request status updated successfully',
                'data' => new TravelRequestResource($travelRequest)
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Travel request status update error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to update travel request status.'
            ], 500);
        }
    }
}
