<?php

namespace App\Services;

use App\Repository\BookCopyRepository;
use App\Repository\ILoanRepository;
use App\Repository\LoanRepository;

class LoanService implements ILoanService
{
    private ILoanRepository $loans;
    private BookCopyRepository $copies;

    public function __construct(
        ?ILoanRepository $loans = null,
        ?BookCopyRepository $copies = null
    ) {
        $this->loans = $loans ?? new LoanRepository();
        $this->copies = $copies ?? new BookCopyRepository();
    }

    public function getActiveLoansForUser(int $userId): array
    {
        return $this->loans->findActiveLoansByUser($userId);
    }

    public function returnBook(int $loanId, int $userId): bool
    {
        return $this->loans->markLoanAsReturned($loanId, $userId);
    }

    public function returnBookForAdmin(int $loanId): bool
    {
        return $this->loans->markLoanAsReturnedByLibrarian($loanId);
    }

    public function borrowBook(int $userId, int $bookId): bool
    {
        $copyId = $this->copies->findAvailableCopyId($bookId);

        if ($copyId === null) {
            return false;
        }

        $dueAt = (new \DateTimeImmutable('+14 days'))->format('Y-m-d H:i:s');
        $newLoanId = $this->loans->createLoan($userId, $copyId, $dueAt);

        return $newLoanId > 0;
    }

    public function countActiveLoans(): int
    {
        return $this->loans->countActiveLoans();
    }

    public function countOverdueLoans(): int
    {
        return $this->loans->countOverdueLoans();
    }

    public function getRecentActiveLoans(int $limit = 5): array
    {
        return $this->loans->findRecentActiveLoans($limit);
    }

    public function getActiveLoansForAdmin(): array
    {
        return $this->loans->findAllActiveLoansWithDetails();
    }

    public function getLoanDetails(int $loanId): ?array
    {
        return $this->loans->findLoanDetails($loanId);
    }
}
