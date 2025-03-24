<?php

namespace Tests\Api\Apply;

use App\Models\Apply;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class IndexTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/apply';

    public function test_get_applies()
    {
        Apply::factory()->count(11)->create(['user_id' => $this->user->user_id]);

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(11, $this->response->json()['total']);
        $this->assertCount(11, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_empty_data()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(0, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_with_keyword_filter()
    {
        Apply::factory()->create(['user_id' => $this->user->user_id, 'occupation' => 'エンジニア']);
        Apply::factory()->create(['user_id' => $this->user->user_id, 'apply_route' => 'Wantedly']);
        Apply::factory()->create(['user_id' => $this->user->user_id, 'memo' => 'メモメモメモ']);
        Apply::factory()->create(['user_id' => $this->user->user_id, 'occupation' => '職種職種']);

        // occupationで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'エンジ'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // apply_routeで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'anted'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // memoで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'メモ'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // マッチしない場合
        $this->json($this->method, $this->path, ['keyword' => 'NonExistent'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->assertEquals(0, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_with_company_id_filter()
    {
        $company1 = Company::factory()->create(['user_id' => $this->user->user_id]);
        $company2 = Company::factory()->create(['user_id' => $this->user->user_id]);

        Apply::factory()->count(2)->create(['user_id' => $this->user->user_id, 'company_id' => $company1->company_id]);
        Apply::factory()->count(1)->create(['user_id' => $this->user->user_id, 'company_id' => $company2->company_id]);

        // マッチする場合
        $this->json($this->method, $this->path, ['company_id' => $company1->company_id], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $this->response->json()['total']);
        $this->assertCount(2, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // マッチしない場合
        $this->json($this->method, $this->path, ['company_id' => 9999999], ['Authorization' => 'Bearer ' . $this->token]);
        $this->assertEquals(0, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_with_status_filter()
    {
        Apply::factory()->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.document_selection')]);
        Apply::factory()->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.exam_selection')]);
        Apply::factory()->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.interview_selection')]);
        Apply::factory()->create(['user_id' => $this->user->user_id, 'status' => config('const.applies.status.offer')]);

        // マッチする場合
        $this->json($this->method, $this->path, ['status' => [config('const.applies.status.document_selection'), config('const.applies.status.interview_selection')]], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, $this->response->json()['total']);
        $this->assertCount(2, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // マッチしない場合
        $this->json($this->method, $this->path, ['status' => [config('const.applies.status.unregistered_selection_process')]], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(0, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_with_pagination()
    {
        Apply::factory()->count(15)->create(['user_id' => $this->user->user_id]);

        // limit = 10, offset = 0
        $this->json($this->method, $this->path, ['offset' => 0, 'limit' => 10], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(15, $this->response->json()['total']);
        $this->assertCount(10, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // limit = 10, offset = 10
        $this->json($this->method, $this->path, ['offset' => 10, 'limit' => 10], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(15, $this->response->json()['total']);
        $this->assertCount(5, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // ページング外の場合
        $this->json($this->method, $this->path, ['offset' => 20, 'limit' => 10], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(15, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_with_multiple_filters()
    {
        $company1 = Company::factory()->create(['user_id' => $this->user->user_id]);
        $company2 = Company::factory()->create(['user_id' => $this->user->user_id]);

        Apply::factory()->create([
            'user_id'     => $this->user->user_id,
            'company_id'  => $company1->company_id,
            'status'      => config('const.applies.status.document_selection'),
            'occupation'  => 'エンジニア',
        ]);

        Apply::factory()->create([
            'company_id'  => $company2->company_id,
            'status'      => config('const.applies.status.interview_selection'),
            'occupation'  => '職種職種',
        ]);

        // 複合条件
        $this->json(
            $this->method,
            $this->path,
            [
                'keyword' => 'エンジ',
                'company_id' => $company1->company_id,
                'status' => [config('const.applies.status.document_selection')]
            ],
            ['Authorization' => 'Bearer ' . $this->token]
        );
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_invalid_token()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_expired_token()
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_applies_user_not_found()
    {
        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
