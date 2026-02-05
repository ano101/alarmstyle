<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTrailingSlash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        // Если URL заканчивается на слеш (кроме корневого пути "/")
        if ($path !== '/' && str_ends_with($path, '/')) {
            // Удаляем trailing slash
            $newPath = rtrim($path, '/');

            // Создаем новый URL без trailing slash
            $url = $request->getSchemeAndHttpHost().$newPath;

            // Добавляем query string, если есть
            if ($request->getQueryString()) {
                $url .= '?'.$request->getQueryString();
            }

            // Постоянный редирект 301
            return redirect($url, 301);
        }

        return $next($request);
    }
}
