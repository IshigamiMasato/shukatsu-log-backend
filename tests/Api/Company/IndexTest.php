<?php

use App\Models\Company;
use Tests\Api\TestCaseWithAuth;

class IndexTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/company';

    public function setUp(): void
    {
        parent::setUp();

        Company::factory()->count(3)->create(['user_id' => $this->user->user_id]);
    }

    public function test_get_companies()
    {
        $this->json($this->method, $this->path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(200);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
