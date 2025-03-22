<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Api\OpenApiTestCase;

class IndexTest extends OpenApiTestCase
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/company';

    public function test_get_companies()
    {
        $user = User::factory()->create([
            'email' => 'tes@tes.com',
            'password' => Hash::make('password'),
        ]);
        Company::factory()->count(3)->create(['user_id' => $user->user_id]);

        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->getAuthToken()]);

        $this->response->assertStatus(200);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    private function getAuthToken(): string
    {
        $this->json('POST', '/api/login', [
            'email'    => 'tes@tes.com',
            'password' => 'password',
        ]);

        $decoded = json_decode($this->response->getContent(), true);

        return $decoded['access_token'];
    }
}
