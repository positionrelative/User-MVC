<?php

namespace App\Controllers\Auth;

use App\Core\Controllers\ApiController;
use App\Services\UserService;

class RegisterController extends ApiController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct()
    {
        $this->userService = UserService::getInstance();
    }

    public function register()
    {
        $data = $this->getJsonInput();

        if (empty($data['name']) || empty($data['email']) || empty(trim($data['password']))) {
            $this->errorResponse('Name, email, and password are required', 422);
        }

        if(filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $this->errorResponse('Invalid email format', 422);
        }

        if(strlen($data['password']) < 6) {
            $this->errorResponse('Password must be at least 6 characters', 422);
        }

        if ($this->userService->checkUserExists($data['email'])) {
            $this->errorResponse('Email already in use', 409);
        }

        try {
            $user = $this->userService->createUser($data);
        } catch (\RuntimeException $e) {
            $this->errorResponse($e->getMessage(), 500);
        }

        $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }
    
}