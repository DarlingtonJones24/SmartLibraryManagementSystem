<?php

namespace App\Repository;

use App\Framework\BaseRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT id, role, name, Email, password_hash, is_blocked
             FROM users
             WHERE id = ?",
            [$id]
        );
    }

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne(
            "SELECT id, role, name, Email, password_hash, is_blocked
             FROM users
             WHERE Email = ?",
            [$email]
        );
    }

    public function countMembers(): int
    {
        $row = $this->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE role = 'member'");
        return (int)($row['cnt'] ?? 0);
    }

    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $sql = 'SELECT id FROM users WHERE Email = ?';
        $params = [$email];

        if ($excludeUserId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeUserId;
        }

        $sql .= ' LIMIT 1';

        return $this->fetchOne($sql, $params) !== null;
    }

    public function createMember(string $name, string $email, string $passwordHash): bool
    {
        return $this->execute(
            'INSERT INTO users (role, name, Email, password_hash, is_blocked, created_at, updated_at)
             VALUES (?, ?, ?, ?, 0, NOW(), NOW())',
            ['member', $name, $email, $passwordHash]
        );
    }

    public function updateProfile(int $userId, string $name, string $email): bool
    {
        return $this->execute(
            'UPDATE users SET name = ?, Email = ?, updated_at = NOW() WHERE id = ?',
            [$name, $email, $userId]
        );
    }
}
