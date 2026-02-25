<?php

namespace App\Models;

class User
{
    public int $id;
    public string $name;
    public string $email;
    public string $role;
    public string $password_hash;
    public bool $is_blocked;
    public bool $is_admin;
    public ?string $created_at;
    public ?string $updated_at;

    public static function fromArray(array $row): self
    {
        $user = new self();

        $user->id = (int)($row['id'] ?? 0);
        $user->name = (string)($row['name'] ?? $row['Name'] ?? '');
        $user->email = (string)($row['email'] ?? $row['Email'] ?? '');
        $user->role = strtolower((string)($row['role'] ?? 'member'));
        $user->password_hash = (string)($row['password_hash'] ?? '');
        $user->is_blocked = (bool)($row['is_blocked'] ?? false);

        $user->created_at = isset($row['created_at']) ? (string)$row['created_at'] : null;
        $user->updated_at = isset($row['updated_at']) ? (string)$row['updated_at'] : null;

        $user->is_admin = $user->isAdmin();

        return $user;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'librarian'], true);
    }
}
