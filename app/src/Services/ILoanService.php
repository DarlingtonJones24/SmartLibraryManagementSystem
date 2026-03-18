<?php

namespace App\Services;

interface ILoanService
{
    public function getActiveLoansForUser(int $userId): array;

    public function returnBook(int $loanId, int $userId): bool;

    public function returnBookForAdmin(int $loanId): bool;

    public function borrowBook(int $userId, int $bookId): bool;

    public function countActiveLoans(): int;

    public function countOverdueLoans(): int;

    public function getRecentActiveLoans(int $limit = 5): array;

    public function getActiveLoansForAdmin(): array;

    public function getLoanDetails(int $loanId): ?array;
}
