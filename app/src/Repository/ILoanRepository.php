<?php

namespace App\Repository;

interface ILoanRepository
{
    public function findActiveLoansByUser(int $userId): array;

    public function markLoanAsReturned(int $loanId, int $userId): bool;

    public function markLoanAsReturnedByLibrarian(int $loanId): bool;

    public function createLoan(int $userId, int $copyId, string $dueAt): int;

    public function countActiveLoans(): int;

    public function countOverdueLoans(): int;

    public function findRecentActiveLoans(int $limit = 5): array;

    public function findLoanDetails(int $loanId): ?array;

    public function findAllActiveLoansWithDetails(): array;

    public function findOverdueBooksByUser(int $userId): array;
}
