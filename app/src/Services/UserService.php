<?php

namespace App\Services;

use App\Framework\Validator;
use App\Repository\IUserRepository;
use App\Repository\UserRepository;

class UserService
{
    private IUserRepository $users;

    public function __construct(?IUserRepository $users = null)
    {
        $this->users = $users ?? new UserRepository();
    }

    public function countMembers(): int
    {
        return $this->users->countMembers();
    }

    public function normalizeSessionUser(?array $user): array
    {
        return [
            'id' => (int) ($user['id'] ?? 0),
            'name' => trim((string) ($user['name'] ?? '')),
            'email' => trim((string) ($user['email'] ?? $user['Email'] ?? '')),
            'role' => trim((string) ($user['role'] ?? '')),
        ];
    }

    public function updateProfile(int $userId, string $name, string $email): array
    {
        $name = trim($name);
        $email = trim($email);

        if ($name === '' || $email === '') {
            return ['success' => false, 'message' => 'Name and email are required.'];
        }

        if (!Validator::email($email)) {
            return ['success' => false, 'message' => 'Invalid email address.'];
        }

        if ($this->users->emailExists($email, $userId)) {
            return ['success' => false, 'message' => 'That email is already in use.'];
        }

        $updated = $this->users->updateProfile($userId, $name, $email);

        return [
            'success' => $updated,
            'message' => $updated ? 'Profile updated.' : 'Failed to update profile.',
        ];
    }
}
