<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\BookService;
use App\Services\IBookService;
use App\ViewModels\CatalogViewModel;

class ApiBooksController extends Controller
{
    private IBookService $bookService;

    public function __construct(?IBookService $bookService = null)
    {
        parent::__construct();
        $this->bookService = $bookService ?? new BookService();
    }

    public function index(): void
    {
        $search = trim($_GET['q'] ?? '');
        $filter = trim($_GET['filter'] ?? '');
        $sort = trim($_GET['sort'] ?? 'title');
        $direction = trim($_GET['direction'] ?? 'asc');
        $userId = Auth::check() ? (int) (Auth::user()['id'] ?? 0) : null;

        try {
            $books = $this->bookService->getCatalogBooks($search, $filter, $sort, $direction, $userId);
        } catch (\Throwable) {
            $this->json(['books' => [], 'message' => 'Unable to load books.'], 500);
        }

        $items = CatalogViewModel::fromBooks('', $search, $filter, $sort, $direction, Auth::check(), $books, 1)->books;

        $this->json([
            'query' => $search,
            'books' => $items,
        ]);
    }
}
