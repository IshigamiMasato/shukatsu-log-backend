<?php

namespace Tests\Api\Document;

use App\Models\Apply;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class UpdateTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'PUT';

    /** @var string */
    private $path = '/api/apply/{apply_id}/document/{document_id}';

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_document(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{document_id}'], [$apply->apply_id, $document->document_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getInvalidPutData
     */
    public function test_update_document_invalid_parameters(array $invalidPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{document_id}'], [$apply->apply_id, $document->document_id], $this->path);

        $this->json($this->method, $path, $invalidPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_BAD_REQUEST);
        $this->assertEquals(config('api.response.code.bad_request'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_document_invalid_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{document_id}'], [$apply->apply_id, $document->document_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_document_expired_token(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{document_id}'], [$apply->apply_id, $document->document_id], $this->path);

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
    public function test_update_document_user_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $path = str_replace(['{apply_id}', '{document_id}'], [$apply->apply_id, $document->document_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_document_apply_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{document_id}'], [$notExistsApplyId, $document->document_id], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    /**
     * @dataProvider getValidPutData
     */
    public function test_update_document_document_not_found(array $validPutData)
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);

        $notExistsDocumentId = 9999999;

        $path = str_replace(['{apply_id}', '{document_id}'], [$apply->apply_id, $notExistsDocumentId], $this->path);

        $this->json($this->method, $path, $validPutData, ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.document_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public static function getValidPutData()
    {
        return [
            [
                [
                    "submission_date" => "2025-03-01",
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
                // submission_dateが存在しない
                [
                    "memo" => "メモメモメモ",
                ]
            ],
            // 無効なファイル形式
            [
                // ファイルのデータ型が配列でない
                [
                    "submission_date" => "2025-03-01",
                    "files" => "Invalid files",
                    "memo" => "メモメモメモ",
                ]
            ],
            [
                // ファイル名のみが存在する場合
                [
                    "submission_date" => "2025-03-01",
                    "files" => [
                        [
                            "name" => "ファイル名",
                        ]
                    ],
                    "memo" => "メモメモメモ",
                ]
            ],
            [
                // ファイルデータのみが存在する場合
                [
                    "submission_date" => "2025-03-01",
                    "files" => [
                        [
                            "base64" => "base64",
                        ]
                    ],
                    "memo" => "メモメモメモ",
                ]
            ],
        ];
    }
}
