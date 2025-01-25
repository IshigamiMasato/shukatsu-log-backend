<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthService
{
    /** @var \App\Repositories\UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validateLogin(array $postedParams): bool|array
    {
        $validator = Validator::make($postedParams, [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ( $validator->fails() ) {
            return ['errors' => $validator->errors()->getMessages()];
        }

        return true;
    }

    public function login(array $postedParams): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->userRepository->findBy( ['email' => $postedParams['email']] );

            // 会員情報 存在確認
            if ($user === null) {
                Log::debug( __METHOD__ . ": user not found. (email = {$postedParams['email']})" );
                return response()->notFound();
            }

            // パスワード検証
            if ( ! Hash::check( $postedParams['password'], $user->password ) ) {
                Log::debug( __METHOD__ . ": missmatch password. (password={$postedParams['password']})" );
                return response()->unauthorized();
            }

            $jti = Str::uuid();
            $jwt = $this->issueJWT( $user->user_id, $jti );

            // トークンリフレッシュ用にJWTを保存しておく
            $redis = Redis::connection('refresh_token');
            $redis->hmset( $jwt, ['user_id' => $user->user_id, 'jti' => $jti] );
            $redis->expire( $jwt, env('JWT_REFRESH_TTL') );

            $result = [
                'access_token' => $jwt,
                'token_type'   => 'bearer',
                'expires_in'   => env('JWT_TTL')
            ];

            return response()->ok($result);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }

    public function refreshToken(string $jwt)
    {
        try {
            $redis = Redis::connection('refresh_token');

            // リフレッシュ可能な期間を過ぎている場合
            if ( ! $redis->exists($jwt) ) {
                return response()->unauthorized(code: 'INVALID_REFRESH_TOKEN', message: '指定されたトークンが不正です。');
            }

            $info = $redis->hgetall($jwt);
            $userId = $info['user_id'];
            $jti    = $info['jti'];

            $newJWT = $this->issueJWT( $userId, $jti );

            // リフレッシュトークン用のトークンを削除
            $redis->del($jwt);

            // リフレッシュ前のトークンをブラックリストへ入れる
            Redis::connection('blacklist_token')
                ->set( $jti, null, 'EX', env('JWT_TTL') ); // キーだけ入れておく

            $result = [
                'access_token' => $newJWT,
                'token_type'   => 'bearer',
                'expires_in'   => env('JWT_TTL')
            ];

            return response()->ok($result);

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return response()->internalServerError();
        }
    }

    private function issueJWT(int $sub, string $jti): string
    {
        $timestamp = time();

        $payload = [
            'iss' => env('APP_URL'),               // トークン発行者
            'aud' => env('APP_URL'),               // トークン使用者
            'sub' => $sub,                         // 認証対象のユーザ識別子
            'iat' => $timestamp,                   // 発行日時
            'nbf' => $timestamp,                   // トークン有効開始日時
            'exp' => $timestamp + env('JWT_TTL'),  // トークン有効期限
            'jti' => $jti,                         // JWTの一意の識別子
        ];

        return JWT::encode( $payload, env('JWT_SECRET'), env('JWT_ALG') );
    }
}
