<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

/**
 * 認証後のユーザー単位レート制限（リミッター steps-user）。
 *
 * `throttle:steps-user` を直接ルートに書くと、Laravel のミドルウェア優先度ソートで
 * 2 つの ThrottleRequests が連続して並べ替えられ、認証より前に実行されてしまう。
 * ThrottleRequests を継承せず委譲することで、ルートに書いた順序（IP → 認証 → ユーザー）を保つ。
 */
class ThrottleStepsUser
{
    public function __construct(private ThrottleRequests $throttle) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $this->throttle->handle($request, $next, 'steps-user');
    }
}
