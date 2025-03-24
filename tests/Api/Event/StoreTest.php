<?php

namespace Tests\Api\Event;

use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class StoreTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/event';

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_event(array $validPostData)
    {
        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPostData
     */
    public function test_store_event_invalid_parameters(array $invalidPostData)
    {
        $this->json($this->method, $this->path, $invalidPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_event_invalid_token(array $validPostData)
    {
        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_event_expired_token(array $validPostData)
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_event_user_not_found(array $validPostData)
    {
        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPostData()
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

    public static function getInvalidPostData()
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
