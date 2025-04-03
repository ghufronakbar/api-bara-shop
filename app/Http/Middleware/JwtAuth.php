<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil header Authorization dari request
        $authHeader = $request->header('Authorization');

        // Pastikan ada Bearer token
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ambil token setelah 'Bearer '
        $token = substr($authHeader, 7);

        try {
            // Dekode token menggunakan JWT_SECRET
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            // Tambahkan decoded data ke request untuk digunakan di controller
            $request->attributes->add(['user' => (array) $decoded]);

        } catch (\Exception $e) {
            // Jika token tidak valid atau kedaluwarsa
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
