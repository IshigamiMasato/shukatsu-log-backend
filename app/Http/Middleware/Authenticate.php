<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Authenticate
{
    use ResponseTrait;

    public function handle($request, Closure $next)
    {
        $jwt = $request->bearerToken();

        if ($jwt === null) {
            return $this->responseUnauthorized();
        }

        // JWT検証
        try {
            $decoded = JWT::decode( $jwt, new Key(env('JWT_SECRET'), env('JWT_ALG')) );

            // ログアウト済 または リフレッシュ済 トークンは無効とする
            $isBlacklist = Redis::connection('blacklist_token')->exists( $decoded->jti );
            if ( $isBlacklist ) {
                throw new Exception( "This token has already been logged out or refreshed. (jti={$decoded->jti})" );
            }

        } catch ( ExpiredException $e ) {
            Log::debug(__METHOD__);
            Log::debug($e);

            return $this->responseUnauthorized( config('api.response.code.expired_token') );

        } catch ( Exception $e ) {
            Log::warning(__METHOD__);
            Log::warning($e);

            return $this->responseUnauthorized();
        }

        $request->merge([
            'user_id' => $decoded->sub,
            'jti'     => $decoded->jti,
        ]);

        return $next($request);
    }
}
