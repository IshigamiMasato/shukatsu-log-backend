<?php

namespace Tests\Api\FinalResult;

use App\Models\Apply;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class StoreTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/apply/{apply_id}/final_result';

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_final_result(array $validPostData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPostData
     */
    public function test_store_final_result_invalid_parameters(array $invalidPostData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, $invalidPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_final_result_invalid_token(array $validPostData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, $validPostData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_final_result_expired_token(array $validPostData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_final_result_user_not_found(array $validPostData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_final_result_apply_not_found(array $validPostData)
    {
        $notExistsApplyId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsApplyId, $this->path);

        $this->json($this->method, $path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPostData()
    {
        return [
            [
                [
                    "status" => 1,
                    "memo" => "メモメモメモ",
                ]
            ],
        ];
    }

    public static function getInvalidPostData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // statusが存在しない
                [
                    "memo" => "メモメモメモ",
                ]
            ],
            // 無効なstatus
            [
                [
                    "status" => 0,
                    "memo" => "メモメモメモ",
                ]
            ],
            [
                [
                    "status" => 4,
                    "memo" => "メモメモメモ",
                ]
            ],
        ];
    }
}
