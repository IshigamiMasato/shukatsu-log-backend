<?php

namespace Tests\Api\Event;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class DeleteTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'DELETE';

    /** @var string */
    private $path = '/api/event/{event_id}';

    public function test_delete_event()
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_event_invalid_token()
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_event_expired_token()
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_event_user_not_found()
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_event_event_not_found()
    {
        $notExistsEventId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsEventId, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.event_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
