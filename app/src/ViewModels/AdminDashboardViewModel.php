<?php

namespace App\ViewModels;

class AdminDashboardViewModel
{
    public string $title;
    public int $totalBooks;
    public int $availableCopies;
    public int $activeLoans;
    public int $overdueLoans;
    public int $pendingReservations;
    public int $totalCopies;
    public array $recentLoans;
    public array $recentReservations;

    public function __construct(
        string $title,
        int $totalBooks,
        int $availableCopies,
        int $activeLoans,
        int $overdueLoans,
        int $pendingReservations,
        int $totalCopies,
        array $recentLoans,
        array $recentReservations
    ) {
        $this->title = $title;
        $this->totalBooks = $totalBooks;
        $this->availableCopies = $availableCopies;
        $this->activeLoans = $activeLoans;
        $this->overdueLoans = $overdueLoans;
        $this->pendingReservations = $pendingReservations;
        $this->totalCopies = $totalCopies;
        $this->recentLoans = $recentLoans;
        $this->recentReservations = $recentReservations;
    }

    public static function fromData(
        string $title,
        int $totalBooks,
        int $availableCopies,
        int $activeLoans,
        int $overdueLoans,
        int $pendingReservations,
        int $totalCopies,
        array $recentLoans,
        array $recentReservations
    ): self {
        $recentLoanItems = AdminLoansViewModel::fromLoans('', $recentLoans)->loans;
        $recentReservationItems = AdminReservationsViewModel::fromReservations('', $recentReservations)->reservations;

        return new self(
            $title,
            $totalBooks,
            $availableCopies,
            $activeLoans,
            $overdueLoans,
            $pendingReservations,
            $totalCopies,
            $recentLoanItems,
            $recentReservationItems
        );
    }
}
