<?php

namespace App\Models;

use App\Core\Database\Model;

class User extends Model {
    protected static string $table = 'users';

    public string $name = '';
    public string $email = '';
    public string $password_hash = '';
    public string $status = 'active';
    public ?string $remember_token = null;
    public ?string $token = null;
    public ?string $token_expires_at = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = []) {
        foreach ($data as $key => $val) {
            if (property_exists($this, $key)) $this->$key = $val;
        }
    }

    /**
     * @param string $email
     * @return self|null
     */
    public static function findByEmail(string $email): ?self
    {
        return static::query()->where('email', '=', $email)->first();
    }

    /**
     * @param string $token
     * @return self|null
     */
    public static function findByRememberToken(string $token): ?self
    {
        return static::query()->where('remember_token', '=', $token)->first();
    }

    /**
     * @param string $token
     * @return self|null
     */
    public static function findByToken(string $token): ?self
    {
        return static::query()
            ->where('token', '=', $token)
            ->where('token_expires_at', '>=', date('Y-m-d H:i:s'))
            ->first();
    }
}