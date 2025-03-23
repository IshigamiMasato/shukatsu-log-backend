<?php

namespace Tests\Api;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class OpenApiTestCase extends TestCase
{
    use DatabaseMigrations;
    use OpenApiAssertions;

    /** @var boolean */
    private $hasOpenApiValidators = false;

    public function setUp(): void
    {
        parent::setUp();

        // テストメソッド毎にOpenAPIのYAMLファイルを読み込みたくないため
        // 既にパリデータが初期化されていれば、テストインスタンス毎に初期化されたバリデータを流用
        if ( ! $this->hasOpenApiValidators ) {
            $this->setOpenApiValidators();
            $this->hasOpenApiValidators = true;
        }
    }
}
