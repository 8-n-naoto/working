<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach (config('security.headers', []) as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        $maxAge = (int) config('security.hsts_max_age');
        if ($maxAge > 0 && app()->isProduction() && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age='.$maxAge);
        }

        return $response;
    }
}
