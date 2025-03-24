<?php

namespace Tests\Api\Event;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class UpdateTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'PUT';

    /** @var string */
    private $path = '/api/event/{event_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_event(array $validPutData)
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_event_invalid_parameters(array $invalidPutData)
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_event_invalid_token(array $validPutData)
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_event_expired_token(array $validPutData)
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_event_user_not_found(array $validPutData)
    {
        $event = Event::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $event->event_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_event_event_not_found(array $validPutData)
    {
        $notExistsEventId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsEventId, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.event_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
    {
        return [
            [
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 5,
                    "start_at" => "2025-02-01 12:00:00",
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ]
        ];
    }

    public static function getInvalidPutData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // titleが存在しない
                [
                    "type"     => 5,
                    "start_at" => "2025-02-01 12:00:00",
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            [
                // typeが存在しない
                [
                    "title"    => "株式会社A 一次面接",
                    "start_at" => "2025-02-01 12:00:00",
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            [
                // start_atが存在しない
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 5,
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            [
                // end_atが存在しない
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 5,
                    "start_at" => "2025-02-01 12:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            // 無効なtype
            [
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 0,
                    "start_at" => "2025-02-01 12:00:00",
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            [
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 7,
                    "start_at" => "2025-02-01 12:00:00",
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            // 無効な日付形式
            [
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 5,
                    "start_at" => "Invalid Date",
                    "end_at"   => "2025-02-01 13:00:00",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
            [
                [
                    "title"    => "株式会社A 一次面接",
                    "type"     => 5,
                    "start_at" => "2025-02-01 12:00:00",
                    "end_at"   => "Invalid Date",
                    "memo"     => "集合場所：〜、持ち物：〜",
                ]
            ],
        ];
    }
}
