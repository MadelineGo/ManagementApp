<?php

namespace App\Infrastructure\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Token no proporcionado o formato inválido.'], 401);
        }

        $jwt = substr($authHeader, 7);

        try {
            // 1. Decodificar y Validar (Firma, Expiración, Emisor)
            $decoded = JWT::decode(
                $jwt,
                new Key(env('SECRET_KEY'), 'HS256')
            );

            // 2. Inyectar el ID de Usuario (Claim 'nameid') en la solicitud para uso posterior
            $request->attributes->set('user_id', $decoded->nameid);
        } catch (\Exception $e) {
            // Capturar errores de expiración (Expired), firma inválida (Signature Invalid), etc.
            Log::warning("JWT Validation Failed: " . $e->getMessage());
            return new JsonResponse(['error' => 'Token inválido o expirado.'], 401);
        }

        // Si es válido, pasa al siguiente middleware o al controlador
        return $next($request);
    }
}
