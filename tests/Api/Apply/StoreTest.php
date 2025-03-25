<?php

namespace Tests\Api\Apply;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class StoreTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/apply';

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_apply(array $validPostData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $validPostData = array_merge($validPostData, ['company_id' => $company->company_id]);

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPostData
     */
    public function test_store_apply_invalid_parameters(array $invalidPostData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        // 都度マイグレーションしているため、company_id=1 となる
        Log::debug( __METHOD__ . ": company_id={$company->company_id}");

        $this->json($this->method, $this->path, $invalidPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_apply_invalid_token(array $validPostData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $validPostData = array_merge($validPostData, ['company_id' => $company->company_id]);

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_apply_expired_token(array $validPostData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $validPostData = array_merge($validPostData, ['company_id' => $company->company_id]);

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
    public function test_store_apply_user_not_found(array $validPostData)
    {
        $company = Company::factory()->create();
        $validPostData = array_merge($validPostData, ['company_id' => $company->company_id]);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_apply_company_not_found(array $validPostData)
    {
        // 他ユーザの企業IDを設定してリクエスト
        $company = Company::factory()->create();
        $validPostData = array_merge($validPostData, ['company_id' => $company->company_id]);

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.company_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPostData()
    {
        return [
            [
                [
                    "company_id"  => 1,
                    "occupation"  => "エンジニア",
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ]
        ];
    }

    public static function getInvalidPostData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // company_idが存在しない
                [
                    "occupation"  => "職種",
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ],
            [
                // occupationが存在しない
                [
                    "company_id"  => 1,
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ],
            // DBに存在しないcompany_id
            [
                [
                    "company_id"  => 9999999,
                    "occupation"  => "エンジニア",
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ],
        ];
    }
}
