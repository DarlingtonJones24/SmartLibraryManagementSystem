<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\BookService;

class BookController extends Controller
{
    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $filter = trim($_GET['filter'] ?? '');
        $sort = trim($_GET['sort'] ?? 'title');
        $direction = trim($_GET['direction'] ?? 'asc');

        try {
            // For overdue/reserved filters, show only current user's items.
            $service = new BookService();
            if ($filter === 'overdue' && \App\Framework\Auth::check()) {
                $user = \App\Framework\Auth::user();
                $lr = new \App\Repository\LoanRepository();
                $loans = $lr->getActiveByUser((int)$user['id']);
                // Keep only overdue loans
                $now = new \DateTimeImmutable();
                $books = [];
                $service = $service ?? new BookService();
                foreach ($loans as $l) {
                    $due = isset($l['due_at']) ? new \DateTimeImmutable($l['due_at']) : null;
                    if ($due !== null && $due < $now) {
                        $bookId = (int)($l['book_id'] ?? $l['bookId'] ?? $l['id'] ?? 0);
                        $bookInfo = $service->getBook($bookId);
                        $books[] = [
                            'id' => $bookId ?: null,
                            'Title' => $l['Title'] ?? $bookInfo['Title'] ?? '',
                            'author' => $l['author'] ?? $bookInfo['author'] ?? '',
                            'cover_url' => $l['cover_url'] ?? $bookInfo['cover_url'] ?? '',
                            'available' => 0,
                            'total_copies' => isset($bookInfo['total_copies']) ? (int)$bookInfo['total_copies'] : (isset($bookInfo['totalCopies']) ? (int)$bookInfo['totalCopies'] : 0)
                        ];
                    }
                }
            } elseif ($filter === 'reserved' && \App\Framework\Auth::check()) {
                $user = \App\Framework\Auth::user();
                $rr = new \App\Repository\ReservationRepository();
                $res = $rr->getByUser((int)$user['id']);
                $books = [];
                $service = $service ?? new BookService();
                foreach ($res as $r) {
                    $bookId = (int)($r['book_id'] ?? $r['bookId'] ?? $r['id'] ?? 0);
                    $bookInfo = $service->getBook($bookId);
                    $books[] = [
                        'id' => $bookId ?: null,
                        'Title' => $r['Title'] ?? $bookInfo['Title'] ?? '',
                        'author' => $r['author'] ?? $bookInfo['author'] ?? '',
                        'cover_url' => $r['cover_url'] ?? $bookInfo['cover_url'] ?? '',
                        'available' => 0,
                        'total_copies' => isset($bookInfo['total_copies']) ? (int)$bookInfo['total_copies'] : (isset($bookInfo['totalCopies']) ? (int)$bookInfo['totalCopies'] : 0)
                    ];
                }
            } else {
                $books = $service->getBooks($search, $filter, $sort, $direction);
            }
        } catch (\Throwable $ex) {
            // Show a friendly error instead of a blank page
            $this->flash('Unable to load catalog. Please ensure the database is running. (' . $ex->getMessage() . ')', 'danger');
            $books = [];
        }

        $this->render('Books/index', [
            'title' => 'Catalog',
            'books' => $books,
            'q' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function detail(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('catalog');
            return;
        }

        $service = new BookService();
        $book = $service->getBook($id);

        if (!$book) {
            $this->flash('Book not found.', 'danger');
            $this->redirect('catalog');
            return;
        }

        $this->render('Books/detail', [
            'title' => $book['Title'] ?? 'Book',
            'book' => $book
        ]);
    }
}
