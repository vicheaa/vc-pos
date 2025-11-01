<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = ['*'];
        $origin = $request->headers->get('Origin');
        $allowOrigin = in_array('*', $allowedOrigins, true) ? '*' : ($origin ?? '');

        $headers = [
            'Access-Control-Allow-Origin' => $allowOrigin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, Accept, Origin',
            'Access-Control-Max-Age' => '86400',
        ];

        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 204);
            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
            return $response;
        }

        /** @var Response $response */
        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        return $response;
    }
}
