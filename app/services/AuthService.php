<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\Models\User;

class AuthService extends BaseService
{
    public function attemptLogin(string $email, string $password): ?User
    {
        $user = \App\Models\User::findByEmail($email);
        if (!$user) return null;

        if (!password_verify($password, $user->password_hash) || $user->status !== 'active') {
            return null;
        }

        return $user;
    }

    public function sendPasswordResetEmail(User $user): void
    {
        $resetToken = bin2hex(random_bytes(16));

        /**
         * @todo implement password reset token storage and email sending
         */
    }

}