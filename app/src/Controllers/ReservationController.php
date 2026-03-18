<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\IReservationService;
use App\Services\ReservationService;

class ReservationController extends Controller
{
    private IReservationService $reservationService;

    public function __construct(?IReservationService $reservationService = null)
    {
        parent::__construct();
        $this->reservationService = $reservationService ?? new ReservationService();
    }

    public function createReservation(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            $this->setMessage('Invalid book.', 'danger');
            $this->redirect('catalog');
            return;
        }

        try {
            $ok = $this->reservationService->createReservation((int) $user['id'], $bookId);
        } catch (\Throwable) {
            $ok = false;
        }

        if (!$ok) {
            $this->setMessage('You already reserved this book.', 'warning');
            $this->redirect('/books/' . $bookId);
            return;
        }

        $this->setMessage('Reservation placed!', 'success');
        if ($this->isAjaxRequest()) {
            $this->json(['success' => $ok, 'message' => 'Reservation placed!']);
        }

        $this->redirect('dashboard');
    }

    public function cancelReservation(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $reservationId = (int)($_POST['reservation_id'] ?? 0);

        if ($reservationId <= 0) {
            $this->setMessage('Invalid reservation.', 'danger');
            $this->redirect('dashboard');
            return;
        }

        $ok = $this->reservationService->cancelReservation($reservationId, (int) $user['id']);

        if ($this->isAjaxRequest()) {
            $this->json(['success' => $ok, 'message' => $ok ? 'Reservation cancelled.' : 'Unable to cancel reservation.']);
        }

        if (!$ok) {
            $this->setMessage('Unable to cancel reservation.', 'danger');
        } else {
            $this->setMessage('Reservation cancelled.', 'success');
        }
        $this->redirect('dashboard/reservation');
    }
}
