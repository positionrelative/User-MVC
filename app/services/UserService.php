<?php

namespace App\Services;

use App\Core\Services\BaseService;
use App\Models\User;

class UserService extends BaseService
{

    /**
     * @param string $email
     * @return bool
     */
    public function checkUserExists(string $email): bool
    {
        return User::query()->where('email', '=', $email)->exists();
    }

    /**
     * @param array $data
     * @return User
     * 
     * @throws \RuntimeException
     */
    public function createUser(array $data): User
    {
        try {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);
            $user->save();

            return $user;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to create user: ' . $e->getMessage());
        }
    }

    public function createToken(User $user): string
    {
        $token = bin2hex(random_bytes(32));
        $user->token = $token;
        $user->token_expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $user->save();

        return $token;
    }

    public function refreshToken(User $user): string
    {
        return $this->createToken($user);
    }

    public function invalidateToken(User $user): void
    {
        $user->token = null;
        $user->token_expires_at = null;
        $user->save();
    }
}
