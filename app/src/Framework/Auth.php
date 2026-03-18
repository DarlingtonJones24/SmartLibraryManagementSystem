<?php

namespace App\Framework;

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        $_SESSION['user'] = $user;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header("Location: /login");
            exit;
        }
    }

    public static function requireLibrarian(): void
    {
        self::requireLogin();
        if ((self::user()['role'] ?? '') !== 'librarian') {
            http_response_code(403);
            echo "Forbidden";
            exit;
        }
    }
}
