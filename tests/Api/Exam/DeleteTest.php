<?php

namespace Tests\Api\Exam;

use App\Models\Apply;
use App\Models\Exam;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class DeleteTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'DELETE';

    /** @var string */
    private $path = '/api/apply/{apply_id}/exam/{exam_id}';

    public function test_delete_exam()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $exam = Exam::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{exam_id}'], [$apply->apply_id, $exam->exam_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_exam_invalid_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $exam = Exam::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{exam_id}'], [$apply->apply_id, $exam->exam_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_exam_expired_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $exam = Exam::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{exam_id}'], [$apply->apply_id, $exam->exam_id], $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_exam_user_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $exam = Exam::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{exam_id}'], [$apply->apply_id, $exam->exam_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_exam_apply_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $exam = Exam::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{exam_id}'], [$notExistsApplyId, $exam->exam_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_exam_exam_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        Exam::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsExamId = 9999999;

        $path = str_replace(['{apply_id}', '{exam_id}'], [$apply->apply_id, $notExistsExamId], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.exam_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
