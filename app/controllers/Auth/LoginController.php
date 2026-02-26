<?php

namespace App\Controllers\Auth;

use App\Core\Controllers\ApiController;
use App\Resources\UserResource;
use App\Services\AuthService;
use App\Services\UserService;

class LoginController extends ApiController
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct()
    {
        $this->authService = AuthService::getInstance();
        $this->userService = UserService::getInstance();
    }

    public function login()
    {
        $input = $this->getJsonInput();
        $email = $input['email'] ?? null;
        $password = $input['password'] ?? null;

        if (empty($email) || empty($password)) {
            $this->errorResponse('Email and password are required', 400);
        }

        $user = $this->authService->attemptLogin($email, $password);

        if (!$user) {
            $this->errorResponse('Invalid credentials', 401);
        }

        $token = $this->userService->createToken($user);

        $this->successResponse([
            'token' => $token,
            'user' => (new UserResource($user))->toArray(),
        ]);
    }
}