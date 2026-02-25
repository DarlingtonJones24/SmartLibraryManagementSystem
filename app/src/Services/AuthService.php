<?php

namespace App\Services;

use App\Repository\IUserRepository;
use App\Repository\UserRepository;

class AuthService implements IAuthService
{
    private IUserRepository $users;

    public function __construct(?IUserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->users->findByEmail($email);

        if (!$user) {
            return null;
        }

        // Stop login if user is blocked
        if (!empty($user['is_blocked']) && (int)$user['is_blocked'] === 1) {
            return null;
        }

        // Verify password hash
        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Return only safe fields for session
        return [
            'id' => (int)$user['id'],
            'name' => $user['name'] ?? ($user['Name'] ?? null),
            'email' => $user['Email'],
            'role' => $user['role']
        ];
    }
}
