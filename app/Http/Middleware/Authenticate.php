<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        $jwt = $request->bearerToken();

        if ($jwt === null) {
            return response()->unauthorized();
        }

        // JWT検証
        try {
            $decoded = JWT::decode( $jwt, new Key(env('JWT_SECRET'), env('JWT_ALG')) );

            // リフレッシュ済のトークンは無効とする
            $isBlacklist = Redis::connection('blacklist_token')->exists( $decoded->jti );
            if ( $isBlacklist ) {
                throw new Exception( "This token has already been refreshed. (jti={$decoded->jti})" );
            }

        } catch ( ExpiredException $e ) {
            Log::debug(__METHOD__);
            Log::debug($e);

            return response()->unauthorized(code: 'EXPIRED_TOKEN');

        } catch ( Exception $e ) {
            Log::warning(__METHOD__);
            Log::warning($e);

            return response()->unauthorized();
        }

        $request->merge(['user_id' => $decoded->sub]);

        return $next($request);
    }
}
