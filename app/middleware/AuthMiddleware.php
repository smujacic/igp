<?php
namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';

        if (strpos($authHeader, 'Bearer ') !== 0) {
            self::unauthorizedResponse();
        }

        $token = substr($authHeader, 7);
        if (!self::isValidToken($token)) {
            self::unauthorizedResponse();
        }
    }

    private static function isValidToken($token) {
        return $token === 'eyJhbGciOiJIUzI1NiJ9.eyJyb2xlIjoiYWxsIn0.MRhlZ2-ZKjhJZ4g0aNl1wFVDfbZCz2OXh0OsCjvKWPs';
    }

    private static function unauthorizedResponse() {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }
}
