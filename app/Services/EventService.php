<?php

namespace App\Services;

use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventService extends Service
{
    /** @var \App\Repositories\EventRepository */
    private $eventRepository;

    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    public function __construct(
        EventRepository $eventRepository,
        UserRepository $userRepository,
    ) {
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    public function index(int $userId): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorUserNotFound();
            }

            $events = $this->eventRepository->getBy(['user_id' => $userId]);

            return $events;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
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
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function store(int $userId, array $postedParams): \App\Models\Event|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorUserNotFound();
            }

            $params = array_merge(['user_id' => $userId], $postedParams);

            $event = $this->eventRepository->create($params);

            return $event;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function validateUpdate(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'title'       => ['required', 'string'],
            'type'        => ['required', 'int', Rule::in(array_values(config('const.event_types')))],
            'start_at'    => ['required', 'date'],
            'end_at'      => ['required', 'date'],
            'memo'        => ['nullable', 'string'],
        ]);

        if ( $validator->fails() ) {
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function update(int $userId, int $eventId, array $postedParams): \App\Models\Event|array
    {
        try {
            $user = $this->userRepository->find($userId);
            if ( $user === null ) {
                Log::error( __METHOD__ . ": User not found. (user_id={$userId})" );
                return $this->errorUserNotFound();
            }

            $event = $this->eventRepository->findBy(['user_id' => $userId, 'event_id' => $eventId]);
            if ( $event === null ) {
                Log::error( __METHOD__ . ": Event not found. (user_id={$userId}, event_id={$eventId})" );
                return $this->errorEventNotFound();
            }

            $isSuccess = $this->eventRepository->update($event, $postedParams);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed update event. (event_id={$eventId}, user_id={$userId}, posted_params=" . json_encode($postedParams) . ")");
            }

            return $event->fresh();

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function delete(int $userId, int $eventId): \Illuminate\Http\JsonResponse
    {
        try {
            $event = $this->eventRepository->findBy(['user_id' => $userId, 'event_id' => $eventId]);

            if ( $event === null ) {
                return response()->notFound();
            }

            $isSuccess = $this->eventRepository->delete($event);

            if ( ! $isSuccess ) {
                throw new Exception( __METHOD__ . ": Failed delete event. (event_id={$eventId})");
            }

            return response()->ok($event);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }
}
