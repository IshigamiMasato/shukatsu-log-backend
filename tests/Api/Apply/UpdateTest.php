<?php

namespace Tests\Api\Apply;

use App\Models\Apply;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class UpdateTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'PUT';

    /** @var string */
    private $path = '/api/apply/{apply_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_apply(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id, 'company_id' => $company->company_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_apply_invalid_parameters(array $invalidPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id, 'company_id' => $company->company_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_apply_invalid_token(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id, 'company_id' => $company->company_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_apply_expired_token(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id, 'company_id' => $company->company_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

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
    public function test_update_apply_user_not_found(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id, 'company_id' => $company->company_id]);

        $path = preg_replace('/{.*}/', $apply->apply_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_apply_apply_not_found(array $validPutData)
    {
        $notExistsApplyId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsApplyId, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
    {
        return [
            [
                [
                    "status"      => 1,
                    "occupation"  => "職種",
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ]
        ];
    }

    public static function getInvalidPutData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // statusが存在しない
                [
                    "occupation"  => "職種",
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ],
            [
                // occupationが存在しない
                [
                    "status"      => 1,
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ],
            // 無効なstatus
            [
                [
                    "status"      => 6,
                    "occupation"  => "職種",
                    "apply_route" => "応募経路",
                    "memo"        => "メモメモメモ",
                ]
            ],
        ];
    }
}
