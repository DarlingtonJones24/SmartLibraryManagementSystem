<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\ILoanService;
use App\Services\LoanService;

class LoanController extends Controller
{
    private ILoanService $loanService;

    public function __construct(?ILoanService $loanService = null)
    {
        parent::__construct();
        $this->loanService = $loanService ?? new LoanService();
    }

    public function borrowBook(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $bookId = (int)($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            $this->setMessage('Invalid book.', 'danger');
            $this->redirect('catalog');
            return;
        }

        $ok = $this->loanService->borrowBook((int) $user['id'], $bookId);

        if (!$ok) {
            if ($this->isAjaxRequest()) {
                $this->json(['success' => false, 'message' => 'No copies available to borrow.']);
            }

            $this->setMessage('No copies available to borrow.', 'warning');
            $this->redirect('/books/' . $bookId);
            return;
        }

        if ($this->isAjaxRequest()) {
            $this->json(['success' => true, 'message' => 'Book borrowed successfully!']);
        }

        $this->setMessage('Book borrowed successfully!', 'success');
        $this->redirect('dashboard');
    }

    public function returnBook(): void
    {
        Auth::requireLogin();

        $user = Auth::user();
        $loanId = (int)($_POST['loan_id'] ?? 0);

        if ($loanId <= 0) {
            $this->setMessage('Invalid loan.', 'danger');
            $this->redirect('dashboard');
            return;
        }

        $ok = $this->loanService->returnBook($loanId, (int) $user['id']);

        if ($this->isAjaxRequest()) {
            $this->json(['success' => (bool) $ok, 'message' => $ok ? 'Book returned.' : 'Unable to return loan.']);
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        if (!$ok) {
            $this->setMessage('Unable to return loan.', 'danger');
            $this->redirect($referer ?? 'dashboard/loans');
            return;
        }

        $this->setMessage('Book returned.', 'success');
        $this->redirect($referer ?? 'dashboard/loans');
    }
}
