<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\ReservationService;

class ReservationController extends Controller
{
    public function reserve(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            $this->flash('Invalid book.', 'danger');
            $this->redirect('catalog');
            return;
        }

        $service = new ReservationService();
        try {
            $ok = $service->reserve((int)$user['id'], $bookId);
        } catch (\Throwable $ex) {
            $ok = false;
        }

        if (!$ok) {
            $this->flash('You already reserved this book.', 'warning');
            $this->redirect('/books/' . $bookId);
            return;
        }

        $this->flash('Reservation placed!', 'success');
        // Return JSON for AJAX requests
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $ok, 'message' => 'Reservation placed!']);
            return;
        }

        $this->redirect('dashboard');
    }

    public function cancel(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $reservationId = (int)($_POST['reservation_id'] ?? 0);

        if ($reservationId <= 0) {
            $this->flash('Invalid reservation.', 'danger');
            $this->redirect('dashboard');
            return;
        }

        $service = new ReservationService();
        $ok = $service->cancel($reservationId, (int)$user['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $ok, 'message' => $ok ? 'Reservation cancelled.' : 'Unable to cancel reservation.']);
            return;
        }

        if (!$ok) {
            $this->flash('Unable to cancel reservation.', 'danger');
        } else {
            $this->flash('Reservation cancelled.', 'success');
        }
        $this->redirect('dashboard/reservation');
    }
}
