<?php

namespace App\Controllers\Auth;

use App\Core\Controllers\ApiController;
use App\Services\AuthService;

class ForgotPasswordController extends ApiController
{
    /**
     * @var AuthService
     */
    private $authService;

    public function __construct()
    {
        $this->authService = AuthService::getInstance();
    }

    public function requestReset()
    {
        $input = $this->getJsonInput();
        $email = $input['email'] ?? null;

        if (empty($email)) {
            $this->errorResponse('Email is required', 400);
        }

        $this->authService->sendPasswordResetEmail($email);

        $this->successResponse(['message' => 'If an account with that email exists, a password reset link has been sent.']);
    }
}