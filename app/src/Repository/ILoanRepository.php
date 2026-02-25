<?php

namespace App\Repository;

interface ILoanRepository
{
    public function getActiveByUser(int $userId): array;

    public function returnLoan(int $loanId, int $userId): bool;

    public function create(int $userId, int $copyId, string $dueAt): int;

    public function countActiveAll(): int;

    public function countOverdue(): int;

    public function getRecentActive(int $limit = 5): array;

    public function getRecent(int $limit = 5): array;

    public function getAllActiveWithDetails(): array;
}
