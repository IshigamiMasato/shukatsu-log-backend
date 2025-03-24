<?php

namespace Tests\Api\Company;

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
    private $path = '/api/company/{company_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_company(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_company_invalid_parameters(array $invalidPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_company_invalid_token(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_company_expired_token(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

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
    public function test_update_company_user_not_found(array $validPutData)
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_company_company_not_found(array $validPutData)
    {
        $notExistsCompanyId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsCompanyId, $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.company_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
    {
        return [
            [
                [
                    "name"                 => "株式会社A",
                    "url"                  => "https://company.com",
                    "president"            => "佐藤 太郎",
                    "address"              => "東京都新宿区Aビル",
                    "establish_date"       => "2000-01-01",
                    "employee_number"      => 100,
                    "listing_class"        => "スタンダード市場",
                    "business_description" => "事業内容",
                    "benefit"              => "福利厚生",
                    "memo"                 => "メモ",
                ]
            ]
        ];
    }

    public static function getInvalidPutData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // nameが存在しない
                [
                    "url" => "https://company.com",
                ]
            ],
            // 無効なURL形式
            [
                [
                    "name" => "株式会社A",
                    "url" => "Invalid URL",
                ]
            ],
            // 無効な日付形式
            [
                [
                    "name" => "株式会社A",
                    "establish_date" => "Invalid Date",
                ]
            ],
            // 数値でない
            [
                [
                    "name" => "株式会社A",
                    "employee_number" => "String",
                ]
            ]
        ];
    }
}
