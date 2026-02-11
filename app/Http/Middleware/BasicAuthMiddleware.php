<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!env('APP_AUTH_ENABLED', false)) {
            return $next($request);
        }

        $username = env('APP_AUTH_USERNAME', 'admin');
        $password = env('APP_AUTH_PASSWORD', 'admin');

        if ($request->getUser() !== $username || $request->getPassword() !== $password) {
            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="ClickHouse Admin"',
            ]);
        }

        return $next($request);
    }
}
