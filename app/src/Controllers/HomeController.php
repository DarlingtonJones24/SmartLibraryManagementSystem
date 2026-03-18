<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\BookService;
use App\Services\IBookService;
use App\Services\LoanService;
use App\Services\ILoanService;
use App\Services\UserService;
use App\ViewModels\CatalogViewModel;
use App\ViewModels\HomeViewModel;

class HomeController extends Controller
{
    private IBookService $bookService;
    private ILoanService $loanService;
    private UserService $userService;

    public function __construct(
        ?IBookService $bookService = null,
        ?ILoanService $loanService = null,
        ?UserService $userService = null
    ) {
        parent::__construct();
        $this->bookService = $bookService ?? new BookService();
        $this->loanService = $loanService ?? new LoanService();
        $this->userService = $userService ?? new UserService();
    }

    public function showHome(): void
    {
        $stats = [
            ['value' => number_format($this->bookService->countBooks()), 'label' => 'Books'],
            ['value' => number_format($this->bookService->countAvailableCopies()), 'label' => 'Available'],
            ['value' => number_format($this->loanService->countActiveLoans()), 'label' => 'Active Loans'],
            ['value' => number_format($this->userService->countMembers()), 'label' => 'Members'],
        ];
        $catalogViewModel = CatalogViewModel::fromBooks(
            'Catalog',
            '',
            '',
            'title',
            'asc',
            false,
            $this->bookService->getCatalogBooks(),
            1
        );
        $homeViewModel = new HomeViewModel('Home', $stats, $catalogViewModel);

        $this->render('Home/index', [
            'title' => $homeViewModel->title,
            'homeViewModel' => $homeViewModel,
        ]);
    }
}
