<?php

namespace App\Services;

use App\Framework\Validator;
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

    public function registerMember(string $name, string $email, string $password, string $confirmPassword): array
    {
        $name = trim($name);
        $email = trim($email);

        if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        if (!Validator::email($email)) {
            return ['success' => false, 'message' => 'Invalid email address.'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }

        if ($this->users->emailExists($email)) {
            return ['success' => false, 'message' => 'An account with that email already exists.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $created = $this->users->createMember($name, $email, $passwordHash);

        return [
            'success' => $created,
            'message' => $created
                ? 'Account created. Please login.'
                : 'Registration failed. Please try again.',
        ];
    }
}
