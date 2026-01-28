<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CallbackThrottle
{
    public function __construct(
        protected RateLimiter $limiter
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $ipKey = 'callback:ip:'.$request->ip();

        $phone = preg_replace('/\D+/', '', (string) $request->input('phone'));
        $phoneKey = $phone ? 'callback:phone:'.$phone : null;

        // IP — 5 запросов / 10 минут
        if ($this->limiter->tooManyAttempts($ipKey, 5)) {
            abort(429);
        }

        // Phone — 2 запроса / 10 минут
        if ($phoneKey && $this->limiter->tooManyAttempts($phoneKey, 2)) {
            abort(429);
        }

        $this->limiter->hit($ipKey, 600); // 10 минут

        if ($phoneKey) {
            $this->limiter->hit($phoneKey, 600);
        }

        return $next($request);
    }
}
