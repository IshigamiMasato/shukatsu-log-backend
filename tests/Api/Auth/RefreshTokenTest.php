<?php

namespace Tests\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\OpenApiTestCase;

class RefreshTokenTest extends OpenApiTestCase
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/token/refresh';

    public function test_refresh_token()
    {
        $email = 'tes@tes.com';
        $pass  = 'password';
        User::factory()->create(['email' => $email, 'password' => Hash::make($pass)]);

        // JWTトークン取得
        $this->json('POST', '/api/login', ['email' => $email, 'password' => $pass]);
        $accessToken = json_decode($this->response->getContent(), true)['access_token'];

        // トークンリフレッシュ
        $this->json($this->method, $this->path, [], ['Authorization' => "Bearer {$accessToken}"]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_refresh_token_missing_refresh_token()
    {
        $this->json($this->method, $this->path, [], []);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_refresh_token_invalid_refresh_token()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => "Bearer invalidRefreshToken"]);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.invalid_refresh_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
