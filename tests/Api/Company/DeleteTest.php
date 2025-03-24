<?php

namespace Tests\Api\Company;

use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class DeleteTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'DELETE';

    /** @var string */
    private $path = '/api/company/{company_id}';

    public function test_delete_company()
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_company_invalid_token()
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_company_expired_token()
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_company_user_not_found()
    {
        $company = Company::factory()->create(['user_id' => $this->user->user_id]);

        $path = preg_replace('/{.*}/', $company->company_id, $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_company_company_not_found()
    {
        $notExistsCompanyId = 9999999;

        $path = preg_replace('/{.*}/', $notExistsCompanyId, $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.company_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
