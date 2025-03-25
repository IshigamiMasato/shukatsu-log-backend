<?php

namespace Tests\Api\Offer;

use App\Models\Apply;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class UpdateTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'PUT';

    /** @var string */
    private $path = '/api/apply/{apply_id}/offer/{offer_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_offer(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_offer_invalid_parameters(array $invalidPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_offer_invalid_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_offer_expired_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_offer_user_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $offer->offer_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_offer_apply_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $offer = Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{offer_id}'], [$notExistsApplyId, $offer->offer_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_offer_offer_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        Offer::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsOfferId = 9999999;

        $path = str_replace(['{apply_id}', '{offer_id}'], [$apply->apply_id, $notExistsOfferId], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.offer_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
    {
        return [
            [
                [
                    "offer_date" => "2025-01-01",
                    "salary" => 10000000,
                    "condition" => "条件条件",
                    "memo" => "メモメモメモ",
                ]
            ],
        ];
    }

    public static function getInvalidPutData()
    {
        return [
            // 必須パラメータが存在しない
            [
                // offer_dateが存在しない
                [
                    "salary" => 10000000,
                    "condition" => "条件条件",
                    "memo" => "メモメモメモ",
                ]
            ],
            // 無効な日付形式
            [
                [
                    "offer_date" => "Invalid Offer Date",
                    "salary" => 10000000,
                    "condition" => "条件条件",
                    "memo" => "メモメモメモ",
                ]
            ],
            // 数値でない
            [
                [
                    "offer_date" => "2025-01-01",
                    "salary" => "String",
                    "condition" => "条件条件",
                    "memo" => "メモメモメモ",
                ]
            ],
        ];
    }
}
