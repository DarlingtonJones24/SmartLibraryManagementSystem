<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\ILoanService;
use App\Services\IReservationService;
use App\Services\LoanService;
use App\Services\NotificationService;
use App\Services\ReservationService;
use App\ViewModels\MemberLoansViewModel;
use App\ViewModels\MemberReservationsViewModel;
use App\ViewModels\NotificationsViewModel;

class ApiUserController extends Controller
{
    private ILoanService $loanService;
    private IReservationService $reservationService;
    private NotificationService $notificationService;

    public function __construct(
        ?ILoanService $loanService = null,
        ?IReservationService $reservationService = null,
        ?NotificationService $notificationService = null
    )
    {
        parent::__construct();
        $this->loanService = $loanService ?? new LoanService();
        $this->reservationService = $reservationService ?? new ReservationService();
        $this->notificationService = $notificationService ?? new NotificationService();
    }

    public function dashboard(): void
    {
        $user = $this->getAuthenticatedUser();
        $loanRows = $this->loanService->getActiveLoansForUser((int) $user['id']);
        $reservationRows = $this->reservationService->getActiveReservationsForUser((int) $user['id']);
        $loans = MemberLoansViewModel::fromLoans('', $loanRows)->loans;
        $reservations = MemberReservationsViewModel::fromReservations('', $reservationRows)->reservations;

        $this->json([
            'loanCount' => count($loans),
            'reservationCount' => count($reservations),
            'loans' => $loans,
            'reservations' => $reservations,
        ]);
    }

    public function returnLoan(): void
    {
        $user = $this->getAuthenticatedUser();
        $data = $this->readJsonBody();
        $loanId = (int) ($data['loan_id'] ?? 0);

        if ($loanId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid loan id.'], 400);
        }

        $ok = $this->loanService->returnBook($loanId, (int) $user['id']);

        if (!$ok) {
            $this->json(['success' => false, 'message' => 'Unable to return loan.'], 400);
        }

        $this->json(['success' => true, 'message' => 'Book returned.']);
    }

    public function cancelReservation(): void
    {
        $user = $this->getAuthenticatedUser();
        $data = $this->readJsonBody();
        $reservationId = (int) ($data['reservation_id'] ?? 0);

        if ($reservationId <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid reservation id.'], 400);
        }

        $ok = $this->reservationService->cancelReservation($reservationId, (int) $user['id']);

        if (!$ok) {
            $this->json(['success' => false, 'message' => 'Unable to cancel reservation.'], 400);
        }

        $this->json(['success' => true, 'message' => 'Reservation cancelled.']);
    }

    public function notifications(): void
    {
        $user = $this->getAuthenticatedUser();
        $rows = $this->notificationService->getNotificationsForUser((int) ($user['id'] ?? 0));
        $viewModel = NotificationsViewModel::fromNotifications('', $rows);

        $this->json([
            'count' => $viewModel->count,
            'notifications' => $viewModel->notifications,
        ]);
    }

    private function getAuthenticatedUser(): array
    {
        if (!Auth::check()) {
            $this->json(['success' => false, 'message' => 'Please login first.'], 401);
        }

        return Auth::user() ?? [];
    }
}
