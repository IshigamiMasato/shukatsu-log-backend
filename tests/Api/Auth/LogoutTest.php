<?php

namespace Tests\Api\Auth;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class LogoutTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/logout';

    public function test_logout()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => "Bearer {$this->token}"]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_logout_invalid_token()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_logout_expired_token()
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
