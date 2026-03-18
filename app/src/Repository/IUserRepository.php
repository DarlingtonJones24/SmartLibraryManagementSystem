<?php

namespace App\Repository;

interface IUserRepository
{
    public function findById(int $id): ?array;

    public function findByEmail(string $email): ?array;

    public function countMembers(): int;

    public function emailExists(string $email, ?int $excludeUserId = null): bool;

    public function createMember(string $name, string $email, string $passwordHash): bool;

    public function updateProfile(int $userId, string $name, string $email): bool;
}
