<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\BookService;
use App\Services\IBookService;
use App\ViewModels\BookDetailViewModel;
use App\ViewModels\CatalogViewModel;

class BookController extends Controller
{
    private IBookService $bookService;

    public function __construct(?IBookService $bookService = null)
    {
        parent::__construct();
        $this->bookService = $bookService ?? new BookService();
    }

    public function showCatalog(): void
    {
        $search = trim($_GET['q'] ?? '');
        $filter = trim($_GET['filter'] ?? '');
        $sort = trim($_GET['sort'] ?? 'title');
        $direction = trim($_GET['direction'] ?? 'asc');
        $page = max(1, (int) ($_GET['p'] ?? 1));
        $userId = Auth::check() ? (int) (Auth::user()['id'] ?? 0) : null;

        try {
            $books = $this->bookService->getCatalogBooks($search, $filter, $sort, $direction, $userId);
        } catch (\Throwable $exception) {
            $this->setMessage('Unable to load catalog. Please ensure the database is running. (' . $exception->getMessage() . ')', 'danger');
            $books = [];
        }

        $catalogViewModel = CatalogViewModel::fromBooks(
            'Catalog',
            $search,
            $filter,
            $sort,
            $direction,
            Auth::check(),
            $books,
            $page
        );

        $this->render('Books/index', [
            'title' => $catalogViewModel->title,
            'catalogViewModel' => $catalogViewModel,
        ]);
    }

    public function showBookDetails(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('catalog');
            return;
        }

        $book = $this->bookService->getBookDetails($id);

        if (!$book) {
            $this->setMessage('Book not found.', 'danger');
            $this->redirect('catalog');
            return;
        }

        $currentUser = Auth::user() ?? [];
        $bookDetailViewModel = BookDetailViewModel::fromBook($book, Auth::check(), (string) ($currentUser['role'] ?? ''));

        $this->render('Books/detail', [
            'title' => $bookDetailViewModel->title,
            'bookDetailViewModel' => $bookDetailViewModel,
        ]);
    }
}
