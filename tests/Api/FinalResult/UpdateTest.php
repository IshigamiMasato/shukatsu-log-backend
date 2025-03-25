<?php

namespace Tests\Api\FinalResult;

use App\Models\Apply;
use App\Models\FinalResult;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class UpdateTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'PUT';

    /** @var string */
    private $path = '/api/apply/{apply_id}/final_result/{final_result_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_final_result(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $finalResult = FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$apply->apply_id, $finalResult->final_result_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_final_result_invalid_parameters(array $invalidPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $finalResult = FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$apply->apply_id, $finalResult->final_result_id], $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_final_result_invalid_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $finalResult = FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$apply->apply_id, $finalResult->final_result_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_final_result_expired_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $finalResult = FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$apply->apply_id, $finalResult->final_result_id], $this->path);

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
    public function test_update_final_result_user_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $finalResult = FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$apply->apply_id, $finalResult->final_result_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_final_result_apply_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $finalResult = FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$notExistsApplyId, $finalResult->final_result_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_final_result_final_result_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsFinalResultId = 9999999;

        $path = str_replace(['{apply_id}', '{final_result_id}'], [$apply->apply_id, $notExistsFinalResultId], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.final_result_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
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

    public static function getInvalidPutData()
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
