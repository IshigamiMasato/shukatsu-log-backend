<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ResponseApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 200: 正常
        Response::macro( 'ok', function ( mixed $data = [] ) {
            return response()->json($data, HttpFoundationResponse::HTTP_OK);
        });

        // 400: パラメータエラー
        Response::macro( 'badRequest', function ( string $code = 'BAD_REQUEST', string $message = '不正なリクエストです。', array $errors = [] ) {
            return response()->json([
                'code'    => $code,
                'message' => $message,
                'errors'  => $errors
            ], HttpFoundationResponse::HTTP_BAD_REQUEST);
        });

        // 401: 認証エラー
        Response::macro( 'unauthorized', function ( string $code = 'UNAUTHORIZED', string $message = '認証に失敗しました。', array $errors = [] ) {
            return response()->json([
                'code'    => $code,
                'message' => $message,
                'errors'  => $errors
            ], HttpFoundationResponse::HTTP_UNAUTHORIZED);
        });

        // 404: リソース不明エラー
        Response::macro( 'notFound', function ( string $code = 'NOT_FOUND', string $message = '対象データが存在しません。', array $errors = [] ) {
            return response()->json([
                'code'    => $code,
                'message' => $message,
                'errors'  => $errors
            ], HttpFoundationResponse::HTTP_NOT_FOUND);
        });

        // 500: システムエラー
        Response::macro( 'internalServerError', function ( string $code = 'INTERNAL_SERVER_ERROR', string $message = 'サーバーエラー', array $errors = [] ) {
            return response()->json([
                'code'    => $code,
                'message' => $message,
                'errors'  => $errors
            ], HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
