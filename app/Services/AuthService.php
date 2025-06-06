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

class AuthService extends Service
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
            return $this->errorBadRequest( $validator->errors()->getMessages() );
        }

        return true;
    }

    public function login(array $postedParams): array
    {
        try {
            // 会員情報 存在確認
            $user = $this->userRepository->findBy( ['email' => $postedParams['email']] );
            if ($user === null) {
                Log::debug( __METHOD__ . ": User not found. (email={$postedParams['email']})" );
                return $this->errorUnAuthorized();
            }

            // パスワード検証
            if ( ! Hash::check( $postedParams['password'], $user->password ) ) {
                Log::debug( __METHOD__ . ": Missmatch password. (email={$postedParams['email']}, password={$postedParams['password']})" );
                return $this->errorUnAuthorized();
            }

            list( $jwt, $jti ) = $this->issueJWT( $user->user_id );

            // トークンリフレッシュ用にJWTを保存しておく
            $redis = Redis::connection('refresh_token');
            $redis->hmset( $jwt, ['user_id' => $user->user_id, 'jti' => $jti] );
            $redis->expire( $jwt, (int) env('JWT_REFRESH_TTL') );

            return [
                'access_token' => $jwt,
                'token_type'   => 'bearer',
                'expires_in'   => (int) env('JWT_TTL'),
            ];

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    public function refreshToken(string $jwt): array
    {
        try {
            $redis = Redis::connection('refresh_token');

            // リフレッシュ可能な期間を過ぎている場合
            if ( ! $redis->exists($jwt) ) {
                return $this->errorUnAuthorized( config('api.response.code.invalid_refresh_token') );
            }

            $info = $redis->hgetall($jwt);
            $userId = $info['user_id'];
            $jti    = $info['jti'];

            list( $newJWT, $newJti ) = $this->issueJWT( $userId );

            // トークンリフレッシュ用にJWTを保存しておく
            $redis->hmset( $newJWT, ['user_id' => $userId, 'jti' => $newJti] );
            $redis->expire( $newJWT, (int) env('JWT_REFRESH_TTL') );

            // リフレッシュしたトークンを削除し、再リフレッシュを無効とする
            $redis->del($jwt);

            // リフレッシュしたトークンをブラックリストへ入れ、再利用を無効とする
            Redis::connection('blacklist_token')
                ->setex( $jti, (int) env('JWT_TTL'), null ); // キーだけ入れておく

            return [
                'access_token' => $newJWT,
                'token_type'   => 'bearer',
                'expires_in'   => (int) env('JWT_TTL'),
            ];

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }

    private function issueJWT(int $sub): array
    {
        $timestamp = time();
        $jti = Str::uuid();

        $payload = [
            'iss' => env('APP_URL'),               // トークン発行者
            'aud' => env('APP_URL'),               // トークン使用者
            'sub' => $sub,                         // 認証対象のユーザ識別子
            'iat' => $timestamp,                   // 発行日時
            'nbf' => $timestamp,                   // トークン有効開始日時
            'exp' => $timestamp + env('JWT_TTL'),  // トークン有効期限
            'jti' => $jti,                         // JWTの一意の識別子
        ];

        return [ JWT::encode( $payload, env('JWT_SECRET'), env('JWT_ALG') ), $jti ];
    }

    public function logout(string $jwt, string $jti): bool|array
    {
        try {
            // トークンリフレッシュを無効とする
            Redis::connection('refresh_token')
                ->del($jwt);

            // ログアウト後に同一トークンでの再ログイン不可とする
            Redis::connection('blacklist_token')
                ->setex( $jti, (int) env('JWT_TTL'), null ); // キーだけ入れておく

            return true;

        } catch ( Exception $e ) {
            Log::error(__METHOD__);
            Log::error($e);

            return $this->errorInternalServerError();
        }
    }
}
