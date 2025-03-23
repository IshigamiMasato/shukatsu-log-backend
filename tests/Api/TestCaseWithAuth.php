<?php

namespace Tests\Api;

use App\Models\User;

abstract class TestCaseWithAuth extends OpenApiTestCase
{
    /** @var string */
    protected $token;

    /** @var \App\Models\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->login($this->user);
    }

    private function login(User $user)
    {
        $this->json('POST', '/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $decoded = json_decode($this->response->getContent(), true);

        return $decoded['access_token'];
    }
}
