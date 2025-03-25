<?php

namespace Tests\Api\Document;

use App\Models\Apply;
use App\Models\Document;
use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Tests\Api\TestCaseWithAuth;

class DeleteFileTest extends TestCaseWithAuth
{
    /** @var string */
    private $method = 'DELETE';

    /** @var string */
    private $path = '/api/apply/{apply_id}/document/{document_id}/file/{file_id}';

    public function test_delete_file()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        $file = File::factory()->create(['document_id' => $document->document_id]);

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$apply->apply_id, $document->document_id, $file->file_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_OK);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_file_invalid_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        $file = File::factory()->create(['document_id' => $document->document_id]);

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$apply->apply_id, $document->document_id, $file->file_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . 'Invalid Token']);

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.unauthorized'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_file_expired_token()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        $file = File::factory()->create(['document_id' => $document->document_id]);

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$apply->apply_id, $document->document_id, $file->file_id], $this->path);

        Carbon::setTestNow(Carbon::now()->addHours(2));

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        Carbon::setTestNow();

        $this->response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertEquals(config('api.response.code.expired_token'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_file_user_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        $file = File::factory()->create(['document_id' => $document->document_id]);

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$apply->apply_id, $document->document_id, $file->file_id], $this->path);

        User::where('email', $this->user->email)->delete();

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.user_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_file_apply_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        $file = File::factory()->create(['document_id' => $document->document_id]);

        $notExistsApplyId = 9999999;

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$notExistsApplyId, $document->document_id, $file->file_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.apply_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_file_document_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        $file = File::factory()->create(['document_id' => $document->document_id]);

        $notExistsDocumentId = 9999999;

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$apply->apply_id, $notExistsDocumentId, $file->file_id], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.document_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }

    public function test_delete_file_file_not_found()
    {
        $apply = Apply::factory()->create(['user_id' => $this->user->user_id]);
        $document = Document::factory()->create(['apply_id' => $apply->apply_id]);
        File::factory()->create(['document_id' => $document->document_id]);

        $notExistsFileId = 9999999;

        $path = str_replace(['{apply_id}', '{document_id}', '{file_id}'], [$apply->apply_id, $document->document_id, $notExistsFileId], $this->path);

        $this->json($this->method, $path, [], ['Authorization' => 'Bearer ' . $this->token]);

        $this->response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals(config('api.response.code.file_not_found'), $this->response->json()['code']);
        $this->assertValidateResponse($this->method, $this->path, $this->response);
    }
}
