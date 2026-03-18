<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\BookService;
use App\Services\IBookService;
use App\Services\ILoanService;
use App\Services\IReservationService;
use App\Services\LoanService;
use App\Services\ReservationService;
use App\ViewModels\AdminBooksViewModel;
use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\AdminLoanDetailViewModel;
use App\ViewModels\AdminLoansViewModel;
use App\ViewModels\AdminReservationsViewModel;

class AdminController extends Controller
{
    private IBookService $bookService;
    private ILoanService $loanService;
    private IReservationService $reservationService;

    public function __construct(
        ?IBookService $bookService = null,
        ?ILoanService $loanService = null,
        ?IReservationService $reservationService = null
    ) {
        parent::__construct();
        $this->bookService = $bookService ?? new BookService();
        $this->loanService = $loanService ?? new LoanService();
        $this->reservationService = $reservationService ?? new ReservationService();
    }

    public function showDashboard(): void
    {
        Auth::requireLibrarian();

        $viewModel = AdminDashboardViewModel::fromData(
            'Admin Dashboard',
            $this->bookService->countBooks(),
            $this->bookService->countAvailableCopies(),
            $this->loanService->countActiveLoans(),
            $this->loanService->countOverdueLoans(),
            $this->reservationService->countPendingReservations(),
            $this->bookService->countTotalCopies(),
            $this->loanService->getRecentActiveLoans(),
            $this->reservationService->getRecentPendingReservations()
        );

        $this->render('Admin/dashboard', [
            'title' => $viewModel->title,
            'adminDashboardViewModel' => $viewModel,
        ]);
    }

    public function showBooksPage(): void
    {
        Auth::requireLibrarian();

        $search = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? 'title');
        $direction = trim($_GET['direction'] ?? 'asc');
        $viewModel = AdminBooksViewModel::fromBooks(
            'Manage Books',
            $search,
            $sort,
            $direction,
            $this->bookService->getCatalogBooks($search, '', $sort, $direction)
        );

        $this->render('Admin/books/index', [
            'title' => $viewModel->title,
            'adminBooksViewModel' => $viewModel,
        ]);
    }

    public function showCreateBookForm(): void
    {
        Auth::requireLibrarian();
        $this->render('Admin/books/create', ['title' => 'Add Book']);
    }

    public function createBook(): void
    {
        Auth::requireLibrarian();

        if (!$this->bookService->createBookWithCopies($_POST)) {
            $this->setMessage('Failed to add book. Make sure the title is filled in.', 'danger');
            $this->redirect('admin/books/create');
            return;
        }

        $this->setMessage('Book and copies added successfully.', 'success');
        $this->redirect('admin/books');
    }

    public function showEditBookForm(): void
    {
        Auth::requireLibrarian();
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->setMessage('Invalid book id.', 'danger');
            $this->redirect('admin/books');
            return;
        }

        $book = $this->bookService->getBookDetails($id);

        if ($book === null) {
            $this->setMessage('Book not found.', 'danger');
            $this->redirect('admin/books');
            return;
        }

        $this->render('Admin/books/edit', ['title' => 'Edit Book', 'book' => $book]);
    }

    public function updateBook(): void
    {
        Auth::requireLibrarian();
        $id = (int) ($_POST['id'] ?? 0);

        if (!$this->bookService->updateBookDetails($id, $_POST)) {
            $this->setMessage('Failed to update book.', 'danger');
            $this->redirect('admin/books');
            return;
        }

        $this->setMessage('Book updated.', 'success');
        $this->redirect('admin/books');
    }

    public function deleteBook(): void
    {
        Auth::requireLibrarian();
        $id = (int) ($_POST['id'] ?? 0);
        $result = $this->bookService->deleteBook($id);

        if (!$result['success']) {
            $this->setMessage($result['message'], 'danger');
            $this->redirect('admin/books');
            return;
        }

        $this->setMessage($result['message'], 'success');
        $this->redirect('admin/books');
    }

    public function showLoansPage(): void
    {
        Auth::requireLibrarian();

        $viewModel = AdminLoansViewModel::fromLoans(
            'Manage Loans',
            $this->loanService->getActiveLoansForAdmin()
        );

        $this->render('Admin/loans/index', [
            'title' => $viewModel->title,
            'adminLoansViewModel' => $viewModel,
        ]);
    }

    public function markLoanReturned(): void
    {
        Auth::requireLibrarian();
        $loanId = (int) ($_POST['loan_id'] ?? 0);

        if ($loanId <= 0) {
            $this->setMessage('Invalid loan id.', 'danger');
            $this->redirect('admin/loans');
            return;
        }

        $ok = $this->loanService->returnBookForAdmin($loanId);

        $this->setMessage(
            $ok ? 'Loan marked as returned.' : 'Unable to mark this loan as returned.',
            $ok ? 'success' : 'danger'
        );
        $this->redirect('admin/loans');
    }

    public function showLoanDetails(): void
    {
        Auth::requireLibrarian();
        $loanId = (int) ($_GET['id'] ?? 0);
        $loan = $this->loanService->getLoanDetails($loanId);

        if ($loan === null) {
            $this->setMessage('Loan not found.', 'danger');
            $this->redirect('admin/loans');
            return;
        }

        $viewModel = AdminLoanDetailViewModel::fromLoan($loan);

        $this->render('Admin/loans/show', [
            'title' => $viewModel->title,
            'adminLoanDetailViewModel' => $viewModel,
        ]);
    }

    public function showReservationsPage(): void
    {
        Auth::requireLibrarian();

        $viewModel = AdminReservationsViewModel::fromReservations(
            'Manage Reservations',
            $this->reservationService->getActiveReservationsForAdmin()
        );

        $this->render('Admin/reservation/index', [
            'title' => $viewModel->title,
            'adminReservationsViewModel' => $viewModel,
        ]);
    }

    public function markReservationReady(): void
    {
        Auth::requireLibrarian();
        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        $ok = $this->reservationService->markReservationReady($reservationId);

        $this->setMessage(
            $ok ? 'Reservation processed and marked ready.' : 'Unable to process reservation.',
            $ok ? 'success' : 'warning'
        );
        $this->redirect('admin/reservation');
    }
}
