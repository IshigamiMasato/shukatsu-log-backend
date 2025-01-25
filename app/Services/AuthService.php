<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

            // トークン発行
            $timestamp = time();
            $payload = [
                'iss'  => env('APP_URL'),               // トークン発行者
                'aud'  => env('APP_URL'),               // トークン使用者
                'sub'  => $user->user_id,               // 認証対象のユーザ識別子
                'iat'  => $timestamp,                   // 発行日時
                'nbf'  => $timestamp,                   // トークン有効開始日時
                'exp'  => $timestamp + env('JWT_TTL'),  // トークン有効期限
            ];

            $jwt = JWT::encode( $payload, env('JWT_SECRET'), env('JWT_ALG') );

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
}
