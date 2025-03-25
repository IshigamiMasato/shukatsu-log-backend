<?php

namespace Tests\Api\Offer;

use App\Models\Apply;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class ShowTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'GET';

    /** @var string */
    private $path = '/api/apply/{apply_id}/offer/{offer_id}';

    public function test_get_offer()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_offer_invalid_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_offer_expired_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_offer_user_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_offer_apply_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{offer_id}'], [$notExistsApplyId, $offer->offer_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_get_offer_offer_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsOfferId = 9999999;

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $notExistsOfferId], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.offer_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
