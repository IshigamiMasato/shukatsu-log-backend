<?php

namespace Tests\Api\Apply;

use App\Models\Apply;
use App\Models\Document;
use App\Models\Exam;
use App\Models\File;
use App\Models\FinalResult;
use App\Models\Interview;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class GetProcessTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/apply/{apply_id}/process';

    public function test_get_processes()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        Document::factory()->create(['apply_id' => $apply->apply_id, 'submission_date' => '2025-01-01']);
        Exam::factory()->create(['apply_id' => $apply->apply_id, 'exam_date' => '2025-02-01']);
        Interview::factory()->create(['apply_id' => $apply->apply_id, 'interview_date' => '2025-02-15']);
        Offer::factory()->create(['apply_id' => $apply->apply_id, 'offer_date' => '2025-03-01']);
        FinalResult::factory()->create(['apply_id' => $apply->apply_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);
        $res = $this->response->json();

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(5, $res);

        // FinalResultの並び順が一番先頭になっているか
        $this->assertEquals(config('const.applies.status.final'), $res[0]['type']);
        // 各日付カラムの降順の並び順になっているか
        $this->assertEquals(config('const.applies.status.offer'), $res[1]['type']);
        $this->assertEquals(config('const.applies.status.interview_selection'), $res[2]['type']);
        $this->assertEquals(config('const.applies.status.exam_selection'), $res[3]['type']);
        $this->assertEquals(config('const.applies.status.document_selection'), $res[4]['type']);

        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_with_one_process()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        // 1件のみレコード作成
        Document::factory()->create(['apply_id' => $apply->apply_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_with_no_processes()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(0, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_with_multiple_same_processes()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        // 面接を2件登録
        Interview::factory()->count(2)->create(['apply_id' => $apply->apply_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_with_files_included()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        // 応募ファイルを3件作成
        File::factory()->count(3)->create(['document_id' => $document->document_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $this->response->json());
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_invalid_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_expired_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_user_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_processes_apply_not_found()
    {
        $notExistsApplyId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsApplyId, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
