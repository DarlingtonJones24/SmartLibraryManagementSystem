<?php

require __DIR__ . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Controllers\AdminController;
use App\Controllers\ApiBooksController;
use App\Controllers\ApiUserController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\LoanController;
use App\Controllers\MemberController;
use App\Controllers\ReservationController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $routes) {
    $routes->addRoute('GET', '/', function (): void {
        if (\App\Framework\Auth::check()) {
            (new DashboardController())->showDashboard();
            return;
        }

        (new BookController())->showCatalog();
    });

    $routes->addRoute('GET', '/index.php', function (): void { (new HomeController())->showHome(); });

    $routes->addRoute('POST', '/index.php', function (): void {
        $route = trim((string) ($_REQUEST['route'] ?? ''));

        switch ($route) {
            case 'reserve':
                (new ReservationController())->createReservation();
                break;
            case 'reserve/cancel':
                (new ReservationController())->cancelReservation();
                break;
            case 'loan/borrow':
                (new LoanController())->borrowBook();
                break;
            case 'loan/return':
                (new LoanController())->returnBook();
                break;
            case 'admin/loan/return':
                (new AdminController())->markLoanReturned();
                break;
            case 'admin/books/create':
                (new AdminController())->createBook();
                break;
            case 'admin/books/edit':
                (new AdminController())->updateBook();
                break;
            case 'admin/books/delete':
                (new AdminController())->deleteBook();
                break;
            case 'admin/reservation/process':
                (new AdminController())->markReservationReady();
                break;
            default:
                http_response_code(404);
                echo '404 - Page Not Found';
                break;
        }
    });

    $routes->addRoute('GET', '/catalog', function (): void { (new BookController())->showCatalog(); });
    $routes->addRoute('GET', '/books', function (): void { (new BookController())->showCatalog(); });
    $routes->addRoute('GET', '/books/{id:\d+}', function (array $vars): void {
        $_GET['id'] = $vars['id'];
        (new BookController())->showBookDetails();
    });
    $routes->addRoute('GET', '/api/books', function (): void { (new ApiBooksController())->index(); });
    $routes->addRoute('GET', '/api/user/dashboard', function (): void { (new ApiUserController())->dashboard(); });
    $routes->addRoute('GET', '/api/user/notifications', function (): void { (new ApiUserController())->notifications(); });
    $routes->addRoute('POST', '/api/user/loan/return', function (): void { (new ApiUserController())->returnLoan(); });
    $routes->addRoute('POST', '/api/user/reservation/cancel', function (): void { (new ApiUserController())->cancelReservation(); });

    $routes->addRoute('GET', '/login', function (): void { (new AuthController())->showLoginForm(); });
    $routes->addRoute('POST', '/login', function (): void { (new AuthController())->login(); });
    $routes->addRoute('GET', '/register', function (): void { (new AuthController())->showRegisterForm(); });
    $routes->addRoute('POST', '/register', function (): void { (new AuthController())->register(); });
    $routes->addRoute('GET', '/logout', function (): void { (new AuthController())->logout(); });

    $routes->addRoute('POST', '/loan/borrow', function (): void { (new LoanController())->borrowBook(); });
    $routes->addRoute('POST', '/loan/return', function (): void { (new LoanController())->returnBook(); });
    $routes->addRoute('POST', '/admin/loan/return', function (): void { (new AdminController())->markLoanReturned(); });
    $routes->addRoute('POST', '/reserve', function (): void { (new ReservationController())->createReservation(); });
    $routes->addRoute('POST', '/reserve/cancel', function (): void { (new ReservationController())->cancelReservation(); });
    $routes->addRoute('POST', '/admin/reservation/process', function (): void { (new AdminController())->markReservationReady(); });

    $routes->addRoute('GET', '/dashboard', function (): void { (new DashboardController())->showDashboard(); });
    $routes->addRoute('GET', '/dashboard/loans', function (): void { (new MemberController())->showLoansPage(); });
    $routes->addRoute('GET', '/dashboard/reservation', function (): void { (new MemberController())->showReservationsPage(); });
    $routes->addRoute('GET', '/loans', function (): void { (new MemberController())->showLoansPage(); });
    $routes->addRoute('GET', '/reservations', function (): void { (new MemberController())->showReservationsPage(); });
    $routes->addRoute('GET', '/notifications', function (): void { (new MemberController())->showNotificationsPage(); });
    $routes->addRoute('GET', '/alerts', function (): void { (new MemberController())->showNotificationsPage(); });
    $routes->addRoute('GET', '/settings', function (): void { (new MemberController())->showSettingsPage(); });
    $routes->addRoute('GET', '/profile/edit', function (): void { (new MemberController())->showEditProfileForm(); });
    $routes->addRoute('POST', '/profile/edit', function (): void { (new MemberController())->updateProfile(); });

    $routes->addRoute('GET', '/admin/dashboard', function (): void { (new AdminController())->showDashboard(); });
    $routes->addRoute('GET', '/admin/books', function (): void { (new AdminController())->showBooksPage(); });
    $routes->addRoute('GET', '/admin/books/create', function (): void { (new AdminController())->showCreateBookForm(); });
    $routes->addRoute('GET', '/admin/books/edit', function (): void { (new AdminController())->showEditBookForm(); });
    $routes->addRoute('GET', '/admin/loans', function (): void { (new AdminController())->showLoansPage(); });
    $routes->addRoute('GET', '/admin/loans/show', function (): void { (new AdminController())->showLoanDetails(); });
    $routes->addRoute('GET', '/loan/detail', function (): void { (new AdminController())->showLoanDetails(); });
    $routes->addRoute('GET', '/admin/reservation', function (): void { (new AdminController())->showReservationsPage(); });
    $routes->addRoute('GET', '/admin/settings', function (): void {
        \App\Framework\Auth::requireLibrarian();
        (new MemberController())->showSettingsPage();
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');

if (isset($_GET['route'])) {
    $legacy = trim((string) $_GET['route'], '/');

    if ($legacy === 'home') {
        $uri = '/';
    } elseif ($legacy === 'catalog') {
        $uri = '/catalog';
    } elseif ($legacy === 'login') {
        $uri = '/login';
    } elseif ($legacy === 'register') {
        $uri = '/register';
    } elseif ($legacy === 'dashboard') {
        $uri = '/dashboard';
    } elseif ($legacy === 'admin/dashboard') {
        $uri = '/admin/dashboard';
    } elseif ($legacy === 'admin/books') {
        $uri = '/admin/books';
    } elseif ($legacy === 'book/detail' && isset($_GET['id'])) {
        $uri = '/books/' . (int) $_GET['id'];
    } elseif ($legacy === 'book/detail' && isset($_GET['book_id'])) {
        $uri = '/books/' . (int) $_GET['book_id'];
    } else {
        $uri = '/' . $legacy;
    }
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Page Not Found';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2] ?? [];
        if ($vars === []) {
            $handler();
        } else {
            $handler($vars);
        }
        break;
}
