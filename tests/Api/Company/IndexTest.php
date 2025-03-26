<?php

namespace Tests\Api\Company;

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
    private $path = '/api/company';

    public function test_get_companies()
    {
        Company::factory()->count(3)->create(['user_id' => $this->user->user_id]);

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(3, $this->response->json()['total']);
        $this->assertCount(3, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_empty_data()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(0, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_with_keyword_filter()
    {
        $company1 = Company::factory()->create(['user_id' => $this->user->user_id, 'name' => '株式会社TESTES']);
        $company2 = Company::factory()->create(['user_id' => $this->user->user_id, 'url' => 'https://tescompany.com']);
        $company3 = Company::factory()->create(['user_id' => $this->user->user_id, 'president' => '佐藤 太郎']);
        $company4 = Company::factory()->create(['user_id' => $this->user->user_id, 'address' => '東京都新宿区Aビル']);
        $company5 = Company::factory()->create(['user_id' => $this->user->user_id, 'listing_class' => 'スタンダード市場']);
        $company6 = Company::factory()->create(['user_id' => $this->user->user_id, 'business_description' => '事業内容']);
        $company7 = Company::factory()->create(['user_id' => $this->user->user_id, 'benefit' => '福利厚生']);
        $company8 = Company::factory()->create(['user_id' => $this->user->user_id, 'memo' => 'メモメモメモ']);

        // nameで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'testes'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company1->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // urlで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'tescompany'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company2->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // presidentで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => '佐藤'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company3->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // addressで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => '新宿'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company4->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // listing_classで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'スタンダード'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company5->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // business_descriptionで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => '事業'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company6->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // benefitで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => '福利厚生'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company7->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // memoで部分一致検索
        $this->json($this->method, $this->path, ['keyword' => 'メモ'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company8->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        // マッチしない場合
        $this->json($this->method, $this->path, ['keyword' => 'NonExistent'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->assertEquals(0, $this->response->json()['total']);
        $this->assertCount(0, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_with_establish_date_filter()
    {
        $company1 = Company::factory()->create(['user_id' => $this->user->user_id, 'establish_date' => '1990-01-01']);
        $company2 = Company::factory()->create(['user_id' => $this->user->user_id, 'establish_date' => '2000-01-01']);
        $company3 = Company::factory()->create(['user_id' => $this->user->user_id, 'establish_date' => '2010-01-01']);

        $this->json($this->method, $this->path, ['from_establish_date' => '2000-01-01'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company2->company_id])
                       ->assertJsonFragment(['company_id' => $company3->company_id]);
        $this->assertEquals(2, $this->response->json()['total']);
        $this->assertCount(2, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        $this->json($this->method, $this->path, ['to_establish_date' => '2000-01-01'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company1->company_id])
                       ->assertJsonFragment(['company_id' => $company2->company_id]);
        $this->assertEquals(2, $this->response->json()['total']);
        $this->assertCount(2, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        $this->json($this->method, $this->path, ['from_establish_date' => '2000-01-01', 'to_establish_date' => '2000-01-01'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company2->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        $this->json($this->method, $this->path, ['from_establish_date' => '1990-01-01', 'to_establish_date' => '2010-01-01'], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(3, $this->response->json()['total']);
        $this->assertCount(3, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_with_employee_number_filter()
    {
        $company1 = Company::factory()->create(['user_id' => $this->user->user_id, 'employee_number' => 100]);
        $company2 = Company::factory()->create(['user_id' => $this->user->user_id, 'employee_number' => 300]);
        $company3 = Company::factory()->create(['user_id' => $this->user->user_id, 'employee_number' => 500]);

        $this->json($this->method, $this->path, ['from_employee_number' => 300], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company2->company_id])
                       ->assertJsonFragment(['company_id' => $company3->company_id]);
        $this->assertEquals(2, $this->response->json()['total']);
        $this->assertCount(2, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        $this->json($this->method, $this->path, ['to_employee_number' => 300], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company1->company_id])
                       ->assertJsonFragment(['company_id' => $company2->company_id]);
        $this->assertEquals(2, $this->response->json()['total']);
        $this->assertCount(2, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        $this->json($this->method, $this->path, ['from_employee_number' => 300, 'to_employee_number' => 300], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company2->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);

        $this->json($this->method, $this->path, ['from_employee_number' => 100, 'to_employee_number' => 500], ['Authorization' => 'Bearer ' . $this->token]);
        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(3, $this->response->json()['total']);
        $this->assertCount(3, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_with_pagination()
    {
        Company::factory()->count(15)->create(['user_id' => $this->user->user_id]);

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

    public function test_get_companies_with_multiple_filters()
    {
        $company1 = Company::factory()->create([
            'user_id' => $this->user->user_id,
            'name' => '株式会社TESTES',
            'establish_date' => '2000-01-01',
            'employee_number' => 100,
        ]);

        $company2 = Company::factory()->create([
            'user_id'     => $this->user->user_id,
            'name' => '株式会社NONEXIST',
            'establish_date' => '2020-01-01',
            'employee_number' => 300,
        ]);

        // 複合条件
        $this->json(
            $this->method,
            $this->path,
            [
                'keyword' => 'testes',
                'from_establish_date' => '2000-01-01',
                'to_establish_date' => '2010-01-01',
                'from_employee_number' => 50,
                'to_employee_number' => 150,
            ],
            ['Authorization' => 'Bearer ' . $this->token]
        );
        $this->response->assertStatus(Response::HTTP_OK)
                       ->assertJsonFragment(['company_id' => $company1->company_id]);
        $this->assertEquals(1, $this->response->json()['total']);
        $this->assertCount(1, $this->response->json()['data']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_invalid_token()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_expired_token()
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_companies_user_not_found()
    {
        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
