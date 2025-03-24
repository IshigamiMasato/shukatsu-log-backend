<?php

namespace Tests\Api\Apply;

use App\Models\Apply;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class GetStatusSummaryTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/apply/status-summary';

    public function test_get_status_summary_with_correct_counts()
    {
        Apply::factory()->count(1)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.unregistered_selection_process')]);
        Apply::factory()->count(1)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.document_selection')]);
        Apply::factory()->count(2)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.exam_selection')]);
        Apply::factory()->count(3)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.interview_selection')]);
        Apply::factory()->count(1)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.offer')]);
        Apply::factory()->count(0)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.final')]);

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                        ->assertJson([
                            'unregistered_selection_process_summary' => "1",
                            'document_selection_summary'             => "1",
                            'exam_selection_summary'                 => "2",
                            'interview_selection_summary'            => "3",
                            'offer_summary'                          => "1",
                            'final_summary'                          => "0",
                        ]);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_status_summary_with_no_records()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK)
                        ->assertJson([
                            'unregistered_selection_process_summary' => "0",
                            'document_selection_summary'             => "0",
                            'exam_selection_summary'                 => "0",
                            'interview_selection_summary'            => "0",
                            'offer_summary'                          => "0",
                            'final_summary'                          => "0",
                        ]);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_status_summary_with_multiple_same_status()
    {
        Apply::factory()->count(3)->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.document_selection')]);

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK)
                        ->assertJson([
                            'unregistered_selection_process_summary' => "0",
                            'document_selection_summary'             => "3",
                            'exam_selection_summary'                 => "0",
                            'interview_selection_summary'            => "0",
                            'offer_summary'                          => "0",
                            'final_summary'                          => "0",
                        ]);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_status_summary_invalid_token()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_status_summary_expired_token()
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_status_summary_user_not_found()
    {
        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
