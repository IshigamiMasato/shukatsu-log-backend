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

    public function test_get_companies_keyword_search()
    {
        Company::factory()->create(['user_id' => $this->user->user_id, 'name' => 'keyword_search']);
        Company::factory()->count(2)->create(['user_id' => $this->user->user_id]);

        $this->json($this->method, $this->path, ['keyword' => 'keyword_search'], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->response->assertJsonFragment(['name' => 'keyword_search']);
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
