<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\LoanService;

class LoanController extends Controller
{
    public function borrow(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            $this->flash('Invalid book.', 'danger');
            $this->redirect('catalog');
            return;
        }

        $service = new LoanService();
        $ok = $service->borrow((int)$user['id'], $bookId);

        if (!$ok) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No copies available to borrow.']);
                return;
            }

            $this->flash('No copies available to borrow.', 'warning');
            $this->redirect('/books/' . $bookId);
            return;
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Book borrowed successfully!']);
            return;
        }

        $this->flash('Book borrowed successfully!', 'success');
        $this->redirect('dashboard');
    }

    public function returnBook(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $loanId = (int)($_POST['loan_id'] ?? 0);

        if ($loanId <= 0) {
            $this->flash('Invalid loan.', 'danger');
            $this->redirect('dashboard');
            return;
        }

        $service = new LoanService();
        $ok = $service->returnBook($loanId, (int)$user['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$ok, 'message' => $ok ? 'Book returned.' : 'Unable to return loan.']);
            return;
        }

        // For normal requests, go back to previous page when possible
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        if (!$ok) {
            $this->flash('Unable to return loan.', 'danger');
            $this->redirect($referer ?? 'dashboard/loans');
            return;
        }

        $this->flash('Book returned.', 'success');
        $this->redirect($referer ?? 'dashboard/loans');
    }
}
