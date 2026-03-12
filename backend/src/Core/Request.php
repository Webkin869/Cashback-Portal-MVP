<?php
namespace App\Core;

class Request
{
    public static function body(): array
    {
        $content = file_get_contents('php://input');
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : $_POST;
    }

    public static function bearerToken(): ?string
    {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            return null;
        }
        return substr($auth, 7);
    }
}
