<?php

namespace Tests\Api\Event;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class IndexTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/event';

    public function test_get_events()
    {
        Event::factory()->count(3)->create(['user_id' => $this->user->user_id]);

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_empty_data()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_with_start_at()
    {
        // 範囲内
        $event = Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-01 10:00:00', 'end_at' => '2025-03-01 12:00:00']);

        // 範囲外
        Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-02-28 12:00:00', 'end_at' => '2025-02-28 14:00:00']);

        $this->json($this->method, $this->path, ['start_at' => '2025-03-01 00:00:00'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                        ->assertJsonFragment(['event_id' => $event->event_id]);

        $this->assertCount(1, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_with_end_at()
    {
        // 範囲内
        $event = Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-01 10:00:00', 'end_at' => '2025-03-01 12:00:00']);

        // 範囲外
        Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-03 12:00:00', 'end_at' => '2025-03-03 14:00:00']);

        $this->json($this->method, $this->path, ['end_at' => '2025-03-01 23:59:59'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                        ->assertJsonFragment(['event_id' => $event->event_id]);

        $this->assertCount(1, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_within_date_range()
    {
        // 範囲内
        $event1 = Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-01 10:00:00', 'end_at' => '2025-03-01 12:00:00']);
        $event2 = Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-02 13:00:00', 'end_at' => '2025-03-02 15:00:00']);

        // 範囲外
        Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-03 12:00:00', 'end_at' => '2025-03-03 14:00:00']);

        $this->json($this->method, $this->path, ['start_at' => '2025-03-01 00:00:00', 'end_at' => '2025-03-02 23:59:59'], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK)
                        ->assertJsonFragment(['event_id' => $event1->event_id])
                        ->assertJsonFragment(['event_id' => $event2->event_id]);
        $this->assertCount(2, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_excludes_out_of_range()
    {
        // 範囲外
        Event::factory()->create(['user_id' => $this->user->user_id, 'start_at' => '2025-03-03 12:00:00', 'end_at' => '2025-03-03 14:00:00']);

        $this->json($this->method, $this->path, ['start_at' => '2025-03-01 00:00:00', 'end_at' => '2025-03-02 23:59:59'], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_invalid_token()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_expired_token()
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_events_user_not_found()
    {
        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
