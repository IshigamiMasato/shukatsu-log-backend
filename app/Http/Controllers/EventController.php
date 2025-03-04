<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Services\EventService;
use Illuminate\Http\Request;

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
        $postedParams = $request->all();

        $events = $this->service->index($userId, $postedParams);
        if ( isset($events['error_code']) ) {
            if ( $events['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
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
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $event = $this->service->store($userId, $postedParams);
        if ( isset($event['error_code']) ) {
            if ( $event['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new EventResource($event) );
    }

    public function update(Request $request, int $eventId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $postedParams = $request->only(['title', 'type', 'start_at', 'end_at', 'memo']);

        $result = $this->service->validateUpdate($postedParams);
        if ( isset($result['error_code']) ) {
            return $this->responseBadRequest( errors: $result['errors'] );
        }

        $event = $this->service->update($userId, $eventId, $postedParams);
        if ( isset($event['error_code']) ) {
            if ( $event['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $event['error_code'] == config('api.response.code.event_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.event_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new EventResource($event) );
    }

    public function delete(Request $request, int $eventId): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user_id;

        $event = $this->service->delete($userId, $eventId);
        if ( isset($event['error_code']) ) {
            if ( $event['error_code'] == config('api.response.code.user_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.user_not_found') );
            }

            if ( $event['error_code'] == config('api.response.code.event_not_found') ) {
                return $this->responseNotFound( code: config('api.response.code.event_not_found') );
            }

            return $this->responseInternalServerError();
        }

        return $this->responseSuccess( new EventResource($event) );
    }
}
