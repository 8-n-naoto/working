<?php

namespace App\Http\Middleware;

use App\Services\StepTokenService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bearer の連携トークンで認証する。AuthenticatesRequests は実装しない
 * （実装すると優先度ソートで throttle より前に移動し、D-007 の順序が崩れる）。
 */
class AuthenticateStepToken
{
    public function __construct(private StepTokenService $tokens) {}

    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->bearerToken();

        $user = $this->tokens->isWellFormed($plain) ? $this->tokens->findUser($plain) : null;

        if ($user === null) {
            throw new AuthenticationException('連携トークンが無効です。');
        }

        Auth::setUser($user);

        return $next($request);
    }
}
