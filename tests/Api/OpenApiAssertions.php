<?php

namespace Tests\Api;

use cebe\openapi\Reader;
use cebe\openapi\ReferenceContext;
use cebe\openapi\spec\OpenApi;
use GuzzleHttp\Psr7\Response;
use Illuminate\Testing\TestResponse;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use PHPUnit\Framework\Assert;
use Throwable;

/**
 * @mixin OpenApiTestCase
 */
trait OpenApiAssertions
{
    /** @var \League\OpenAPIValidation\PSR7\RequestValidator */
    protected $requestValidator;

    /** @var \League\OpenAPIValidation\PSR7\ResponseValidator */
    protected $responseValidator;

    protected function setOpenApiValidators(): void
    {
        // $refで参照している部分を解決し、スキーマを取得
        $openApi = Reader::readFromYamlFile( __DIR__ . '/../../docs/openapi/openapi.yaml', OpenApi::class, ReferenceContext::RESOLVE_MODE_ALL);

        $validatorBuilder = (new ValidatorBuilder)->fromSchema($openApi);

        $this->requestValidator  = $validatorBuilder->getRequestValidator();
        $this->responseValidator = $validatorBuilder->getResponseValidator();
    }

    /**
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    protected function assertValidateResponse(string $method, string $path, TestResponse $response): void
    {
        // 対象のAPIを示すoperator
        $operation = new OperationAddress($path, strtolower($method));
        $psr7Response = $this->convertToPsr7Response($response);

        try {
            $this->responseValidator->validate($operation, $psr7Response);

        } catch ( ValidationFailed $e ) {
            $errorContext = $this->makeErrorContext($e);

            // validationに失敗した場合はテストを失敗させる
            Assert::fail($errorContext);
        }
    }

    private function convertToPsr7Response(TestResponse $response): Response
    {
        $psr7Response = new Response(
            $response->getStatusCode(),
            $response->headers->all(),
            $response->getContent(),
        );

        return $psr7Response;
    }

    private function makeErrorContext(Throwable $e): string
    {
        $errorContext = '';

        while ($e) {
            $errorContext .= $e->getMessage() . "\n";
            $e = $e->getPrevious();
        }

        return $errorContext;
    }
}
