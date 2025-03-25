<?php

namespace Tests\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\OpenApiTestCase;

class LoginTest extends OpenApiTestCase
{
    /** @var string */
    private $method = 'POST';

    /** @var string */
    private $path = '/api/login';

    public function test_login()
    {
        $email = 'tes@tes.com';
        $pass  = 'password';
        User::factory()->create(['email' => $email, 'password' => Hash::make($pass)]);

        $this->json($this->method, $this->path, ['email' => $email, 'password' => $pass]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPostData
     */
    public function test_login_invalid_parameters(array $invalidPostData)
    {
        $this->json($this->method, $this->path, $invalidPostData);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_login_not_exists_email()
    {
        $email = 'tes@tes.com';
        $pass  = 'password';
        User::factory()->create(['email' => $email, 'password' => Hash::make($pass)]);

        $this->json($this->method, $this->path, ['email' => 'not-exists-mail@tes.com', 'password' => $pass]);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_login_missmatch_password()
    {
        $email = 'tes@tes.com';
        $pass  = 'password';
        User::factory()->create(['email' => $email, 'password' => Hash::make($pass)]);

        $this->json($this->method, $this->path, ['email' => $email, 'password' => 'missmatch-password']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getInvalidPostData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // emailが存在しない
                [
                    "password" => "password",
                ]
            ],
            [
                // passwordが存在しない
                [
                    "email" => "tes@tes.com",
                ]
            ],
            // 無効なメールアドレス形式
            [
                [
                    "email" => "Invalid Email",
                    "password" => "password",
                ]
            ],
        ];
    }
}
