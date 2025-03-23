<?php

namespace Tests\Api\Company;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class StoreTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/company';

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_company(array $validPostData)
    {
        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPostData
     */
    public function test_store_company_invalid_parameters(array $invalidPostData)
    {
        $this->json($this->method, $this->path, $invalidPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_company_invalid_token(array $validPostData)
    {
        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPostData
     */
    public function test_store_company_expired_token(array $validPostData)
    {
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
    public function test_store_company_user_not_found(array $validPostData)
    {
        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, $validPostData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPostData()
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

    public static function getInvalidPostData()
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
