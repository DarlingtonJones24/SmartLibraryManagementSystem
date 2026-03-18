<?php

namespace App\ViewModels;

use App\Framework\Auth;
use App\Services\NotificationService;

class LayoutViewModel
{
    public bool $isLoggedIn;
    public bool $isLibrarian;
    public string $displayName;
    public string $initials;
    public int $notificationCount;
    public string $currentPath;

    public function __construct(
        bool $isLoggedIn,
        bool $isLibrarian,
        string $displayName,
        string $initials,
        int $notificationCount,
        string $currentPath
    ) {
        $this->isLoggedIn = $isLoggedIn;
        $this->isLibrarian = $isLibrarian;
        $this->displayName = $displayName;
        $this->initials = $initials;
        $this->notificationCount = $notificationCount;
        $this->currentPath = $currentPath;
    }

    public static function fromRequest(): self
    {
        $isLoggedIn = Auth::check();
        $user = $isLoggedIn ? (Auth::user() ?? []) : [];
        $displayName = $isLoggedIn ? self::displayName($user) : '';

        return new self(
            $isLoggedIn,
            in_array(strtolower((string) ($user['role'] ?? '')), ['librarian', 'admin'], true),
            $displayName,
            $displayName !== '' ? self::initials($displayName) : '',
            self::notificationCount($isLoggedIn, $user),
            self::currentPath()
        );
    }

    private static function notificationCount(bool $isLoggedIn, array $user): int
    {
        if (!$isLoggedIn) {
            return 0;
        }

        $userId = (int) ($user['id'] ?? 0);

        if ($userId <= 0) {
            return 0;
        }

        try {
            return (new NotificationService())->countNotificationsForUser($userId);
        } catch (\Throwable) {
            return 0;
        }
    }

    private static function displayName(array $user): string
    {
        $name = trim((string) ($user['name'] ?? ''));

        if ($name !== '') {
            return $name;
        }

        $email = trim((string) ($user['email'] ?? $user['Email'] ?? ''));

        if ($email === '') {
            return 'User';
        }

        return (string) strtok($email, '@');
    }

    private static function initials(string $displayName): string
    {
        $parts = preg_split('/\s+/', trim($displayName)) ?: [];
        $initials = strtoupper(
            substr($parts[0] ?? '', 0, 1)
            . (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
        );

        if ($initials !== '') {
            return $initials;
        }

        return strtoupper(substr($displayName, 0, 1));
    }

    private static function currentPath(): string
    {
        $path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        $path = rtrim($path, '/');

        if ($path === '') {
            return '/';
        }

        return $path;
    }
}
