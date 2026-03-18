<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\BookService;
use App\Services\IBookService;
use App\Services\UserService;
use App\ViewModels\CatalogViewModel;
use App\ViewModels\MemberDashboardViewModel;

class DashboardController extends Controller
{
    private IBookService $bookService;
    private UserService $userService;

    public function __construct(?IBookService $bookService = null, ?UserService $userService = null)
    {
        parent::__construct();
        $this->bookService = $bookService ?? new BookService();
        $this->userService = $userService ?? new UserService();
    }

    public function showDashboard(): void
    {
        Auth::requireLogin();

        $user = $this->userService->normalizeSessionUser(Auth::user());
        $search = trim($_GET['q'] ?? '');
        $filter = trim($_GET['filter'] ?? '');
        $sort = trim($_GET['sort'] ?? 'title');
        $direction = trim($_GET['direction'] ?? 'asc');
        $page = max(1, (int) ($_GET['p'] ?? 1));

        try {
            $books = $this->bookService->getCatalogBooks($search, $filter, $sort, $direction, (int) $user['id']);
        } catch (\Throwable $exception) {
            $books = [];
            $this->setMessage('Unable to load catalog. Please ensure the database is running. (' . $exception->getMessage() . ')', 'danger');
        }

        $catalogViewModel = CatalogViewModel::fromBooks(
            'Catalog',
            $search,
            $filter,
            $sort,
            $direction,
            true,
            $books,
            $page
        );
        $memberDashboardViewModel = new MemberDashboardViewModel(
            'Dashboard',
            $user['name'] !== '' ? $user['name'] : $user['email'],
            $catalogViewModel
        );

        $this->render('MemberDashboard/dashboard', [
            'title' => $memberDashboardViewModel->title,
            'memberDashboardViewModel' => $memberDashboardViewModel,
        ]);
    }
}
