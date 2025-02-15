<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /** @var \App\Services\EventService */
    private $service;

    public function __construct(EventService $eventService)
    {
        $this->service = $eventService;
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $events = $this->service->index($userId);

        if ( isset($events['error_code']) ) {
            if ( $events['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( EventResource::collection($events) );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only(['title', 'type', 'start_at', 'end_at', 'memo']);

        $result = $this->service->validateStore($postedParams);
        if ( isset($result['errors']) ) {
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->store($userId, $postedParams);
    }

    public function update(Request $request, int $eventId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only(['title', 'type', 'start_at', 'end_at', 'memo']);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['errors']) ) {
            return response()->badRequest( errors: $result['errors'] );
        }

        return $this->service->update($userId, $eventId, $postedParams);
    }

    public function delete(Request $request, int $eventId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        return $this->service->delete($userId, $eventId);
    }
}
