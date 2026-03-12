<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;

class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    public function register(): void
    {
        $result = $this->auth->register(Request::body());
        if (isset($result['error'])) {
            Response::json(['message' => $result['error']], 422);
        }
        Response::json($result, 201);
    }

    public function login(): void
    {
        $body = Request::body();
        $result = $this->auth->login($body['email'] ?? '', $body['password'] ?? '');
        if (isset($result['error'])) {
            Response::json(['message' => $result['error']], 401);
        }
        Response::json($result);
    }

    public function me(): void
    {
        $user = $this->auth->userFromToken(Request::bearerToken());
        if (!$user) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
        Response::json(['user' => $user]);
    }

    public function forgotPassword(): void
    {
        Response::json(['message' => 'Password reset flow placeholder. Real email integration qo‘shilishi kerak.']);
    }
}
