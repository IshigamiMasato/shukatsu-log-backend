<?php

namespace Tests\Api\Interview;

use App\Models\Apply;
use App\Models\Interview;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class UpdateTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'PUT';

    /** @var string */
    private $path = '/api/apply/{apply_id}/interview/{interview_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_interview(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $interview = Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{interview_id}'], [$apply->apply_id, $interview->interview_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_interview_invalid_parameters(array $invalidPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $interview = Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{interview_id}'], [$apply->apply_id, $interview->interview_id], $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_interview_invalid_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $interview = Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{interview_id}'], [$apply->apply_id, $interview->interview_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_interview_expired_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $interview = Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{interview_id}'], [$apply->apply_id, $interview->interview_id], $this->path);

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
    public function test_update_interview_user_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $interview = Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{interview_id}'], [$apply->apply_id, $interview->interview_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_interview_apply_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $interview = Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{interview_id}'], [$notExistsApplyId, $interview->interview_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_interview_interview_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        Interview::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsInterviewId = 9999999;

        $path = str_replace(['{apply_id}', '{interview_id}'], [$apply->apply_id, $notExistsInterviewId], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.interview_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
    {
        return [
            [
                [
                    "interview_date" => "2025-01-01",
                    "interviewer_info" => "面接官情報面接官情報",
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
                // interview_dateが存在しない
                [
                    "interviewer_info" => "面接官情報面接官情報",
                    "memo" => "メモメモメモ",
                ]
            ],
            // 無効な日付形式
            [
                [
                    "interview_date" => "Invalid Interview Date",
                    "interviewer_info" => "面接官情報面接官情報",
                    "memo" => "メモメモメモ",
                ]
            ],
        ];
    }
}
