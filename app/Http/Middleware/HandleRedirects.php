<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        // Ищем активный редирект для данного пути
        $redirect = Redirect::findActiveRedirect($path);

        if ($redirect) {
            // Добавляем query string, если есть
            $url = $redirect->to_url;
            if ($request->getQueryString()) {
                $url .= '?'.$request->getQueryString();
            }

            return redirect($url, $redirect->status_code);
        }

        return $next($request);
    }
}
