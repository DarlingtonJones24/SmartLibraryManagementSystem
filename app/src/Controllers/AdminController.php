<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\BookService;
use App\Repository\BookRepository;
use App\Repository\LoanRepository;
use App\Repository\ReservationRepository;
use App\Services\ReservationService;

class AdminController extends Controller
{
    private BookService $bookService;
    private ReservationService $reservationService;
    private BookRepository $bookRepo;
    private LoanRepository $loanRepo;

    public function __construct()
    {
        // Initialize dependencies
        $this->bookService = new BookService();
        $this->bookRepo = new BookRepository();
        $this->loanRepo = new LoanRepository();
        $this->reservationService = new ReservationService(new ReservationRepository());
    }

    public function dashboard(): void
    {
        Auth::requireLibrarian();

        // 1. Get stats from Repositories (No raw SQL here!)
        $totalBooks = $this->bookRepo->countAll();
        $availableTitles = $this->bookRepo->countAvailable();
        $activeLoansCount = $this->loanRepo->countActiveAll();
        
        // Sum total copies via repository method
        $totalCopies = $this->bookRepo->getTotalPhysicalCopiesCount();
        $availableCopies = max(0, $totalCopies - $activeLoansCount);
        
        // Use repository for specific counts
        $overdueCount = $this->loanRepo->countOverdue();
        $waitingReservations = $this->reservationService->getWaitingCount();

        // 2. Get Recent Lists
        $recentLoans = $this->loanRepo->getRecentActive(5);
        $recentReservations = $this->reservationService->getRecentForAdminDashboard();

        $this->render('Admin/dashboard', [
            'title' => 'Admin Dashboard',
            'totalBooks' => $totalBooks,
            'available' => $availableTitles,
            'activeLoans' => $activeLoansCount,
            'totalCopies' => $totalCopies,
            'availableCopies' => $availableCopies,
            'overdue' => $overdueCount,
            'reservations' => $waitingReservations,
            'recentLoans' => $recentLoans,
            'recentReservations' => $recentReservations,
        ]);
    }

    public function books(): void
    {
        Auth::requireLibrarian();

        $search = trim($_GET['q'] ?? '');
        $sort = trim($_GET['sort'] ?? 'title');
        $direction = trim($_GET['direction'] ?? 'asc');

        $books = $this->bookService->getBooks($search, '', $sort, $direction);

        $this->render('Admin/books/index', [
            'title' => 'Manage Books',
            'books' => $books,
            'q' => $search,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function showCreateForm(): void
    {
        Auth::requireLibrarian();
        $this->render('Admin/books/create', ['title' => 'Add Book']);
    }

    public function create(): void
    {
        Auth::requireLibrarian();

        $data = $_POST; // Let the Service handle the trimming and logic
        
        if (empty(trim($data['Title'] ?? ''))) {
            $this->flash('Title is required.', 'danger');
            $this->redirect('admin/books/create');
            return;
        }

        // Move the complex logic of creating a book + copies to the Service
        $ok = $this->bookService->createFullBookEntry($data);

        if ($ok) {
            $this->flash('Book and copies added successfully.', 'success');
        } else {
            $this->flash('Failed to add book. Check system logs.', 'danger');
        }

        $this->redirect('admin/books');
    }

    public function showEditForm(): void
    {
        Auth::requireLibrarian();
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->flash('Invalid book id.', 'danger');
            $this->redirect('admin/books');
            return;
        }

        $book = $this->bookService->getBook($id);
        $this->render('Admin/books/edit', ['title' => 'Edit Book', 'book' => $book]);
    }

    public function edit(): void
    {
        Auth::requireLibrarian();
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->flash('Invalid book id.', 'danger');
            $this->redirect('admin/books');
            return;
        }

        $ok = $this->bookService->updateBook($id, $_POST);

        if ($ok) {
            $this->flash('Book updated.', 'success');
        } else {
            $this->flash('Failed to update book.', 'danger');
        }

        $this->redirect('admin/books');
    }

    public function delete(): void
    {
        Auth::requireLibrarian();
        $id = (int)($_POST['id'] ?? 0);

        $ok = ($id > 0) ? $this->bookService->deleteBook($id) : false;

        if ($ok) {
            $this->flash('Book deleted.', 'success');
        } else {
            $this->flash('Failed to delete book.', 'danger');
        }

        $this->redirect('admin/books');
    }

    public function loansPage(): void
    {
        Auth::requireLibrarian();
        $loans = $this->loanRepo->getAllActiveWithDetails();
        $this->render('Admin/loans/index', ['title' => 'Manage Loans', 'loans' => $loans]);
    }

    public function reservationsPage(): void
    {
        Auth::requireLibrarian();
        $reservations = $this->reservationService->getAllActiveReservations();
        $this->render('Admin/reservation/index', ['title' => 'Manage Reservations', 'reservations' => $reservations]);
    }

    public function processReservation(): void
    {
        Auth::requireLibrarian();
        $reservationId = (int)($_POST['reservation_id'] ?? 0);

        $ok = $this->reservationService->markAsReady($reservationId);

        if ($ok) {
            $this->flash('Reservation processed and marked ready.', 'success');
        } else {
            $this->flash('Unable to process reservation.', 'warning');
        }

        $this->redirect('admin/reservation');
    }
}