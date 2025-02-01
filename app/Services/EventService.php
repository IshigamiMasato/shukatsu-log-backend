<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\EventRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventService
{
    /** @var \App\Repositories\EventRepository */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index(int $userId): \Illuminate\Http\JsonResponse
    {
        try {
            $events = $this->eventRepository->getBy(['user_id' => $userId]);

            return response()->ok($events);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }

    public function validateStore(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'title'       => ['required', 'string'],
            'type'        => ['required', 'int', Rule::in(array_values(config('const.event_types')))],
            'start_at'    => ['required', 'date'],
            'end_at'      => ['required', 'date'],
            'memo'        => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return ['errors' => $validator->errors()->getMessages()];
        }

        return true;
    }

    public function store(int $userId, array $postedParams): \Illuminate\Http\JsonResponse
    {
        try {
            $params = array_merge(['user_id' => $userId], $postedParams);

            $event = $this->eventRepository->create($params);

            return response()->ok($event);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }
}
