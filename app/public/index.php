<?php

require __DIR__ . '/../vendor/autoload.php';

\App\Framework\TempData::start();

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\HomeController;
use App\Controllers\LoanController;
use App\Controllers\ReservationController;
use App\Controllers\DashboardController;
use App\Framework\Auth;
use App\Services\LoanService;
use App\Services\ReservationService;
use App\Controllers\AdminController;

function render(string $viewPath, array $vars = []): void {
    \App\Framework\TempData::start();
    $vars['flash'] = \App\Framework\TempData::get('flash');
    extract($vars, EXTR_SKIP);

    $baseViews = __DIR__ . '/../src/Views';
    // I support both style formats here:
    // - Framework paths like 'Books/index'
    // - Legacy paths like 'Auth/login.php'
    if (str_ends_with($viewPath, '.php')) {
        $full = $baseViews . '/' . ltrim($viewPath, '/');

        if (!is_file($full)) {
            http_response_code(500);
            echo "View not found: " . htmlspecialchars($viewPath);
            return;
        }

        require $baseViews . '/Shared/header.php';
        require $baseViews . '/Shared/navbar.php';
        require $full;
        require $baseViews . '/Shared/footer.php';
        return;
    }

    // Use the framework renderer for normal view paths
    \App\Framework\View::render($viewPath, $vars);
}

$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    // Home route: logged-in users go to dashboard, guests go to catalog
    $r->addRoute('GET', '/', function() {
        if (\App\Framework\Auth::check()) {
            (new DashboardController())->index();
            return;
        }

        (new BookController())->index();
    });
    $r->addRoute('GET', '/index.php', function() { (new HomeController())->index(); }); // keep /index.php working
    // Legacy POST support for /index.php?route=...
    $r->addRoute('POST', '/index.php', function() {
        // Read route from query string or POST body
        $route = trim($_REQUEST['route'] ?? '');
        switch ($route) {
            case 'reserve':
                (new ReservationController())->reserve();
                break;
            case 'reserve/cancel':
                (new ReservationController())->cancel();
                break;
            case 'loan/borrow':
                (new LoanController())->borrow();
                break;
            case 'loan/return':
                (new LoanController())->returnBook();
                break;
            case 'admin/books/create':
                (new \App\Controllers\AdminController())->create();
                break;
            case 'admin/books/edit':
                (new \App\Controllers\AdminController())->edit();
                break;
            case 'admin/books/delete':
                (new \App\Controllers\AdminController())->delete();
                break;
            case 'admin/reservation/process':
                (new \App\Controllers\AdminController())->processReservation();
                break;
            default:
                http_response_code(404);
                echo '404 - Page Not Found';
                break;
        }
    });

    // Catalog routes
    $r->addRoute('GET', '/catalog', function() { (new BookController())->index(); });
    $r->addRoute('GET', '/books', function() { (new BookController())->index(); });
    $r->addRoute('GET', '/books/{id:\\d+}', function($vars) { $_GET['id'] = $vars['id']; (new BookController())->detail(); });

    // Auth routes
    $r->addRoute('GET', '/login', function() { (new AuthController())->loginForm(); });
    $r->addRoute('POST', '/login', function() { (new AuthController())->loginPost(); });
    // Forgot password page (simple placeholder)
    $r->addRoute('GET', '/forgot-password', function() { render('Auth/forgot-password.php'); });
    $r->addRoute('POST', '/forgot-password', function() {
        // Simple flow: accept email then go back to login with flash
        $email = trim($_POST['email'] ?? '');
        if ($email !== '') {
            \App\Framework\TempData::start();
            \App\Framework\TempData::set('flash', ['message' => 'If that email exists we sent reset instructions (demo).', 'type' => 'success']);
        }
        header('Location: /login');
        exit;
    });
    // Register page and submit handler
    $r->addRoute('GET', '/register', function() { render('Auth/register.php'); });
    $r->addRoute('POST', '/register', function() {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password2 = trim($_POST['password2'] ?? '');

        if ($name === '' || $email === '' || $password === '' || $password2 === '') {
            \App\Framework\TempData::start();
            \App\Framework\TempData::set('flash', ['message' => 'All fields are required.', 'type' => 'danger']);
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \App\Framework\TempData::start();
            \App\Framework\TempData::set('flash', ['message' => 'Invalid email address.', 'type' => 'danger']);
            header('Location: /register');
            exit;
        }

        if ($password !== $password2) {
            \App\Framework\TempData::start();
            \App\Framework\TempData::set('flash', ['message' => 'Passwords do not match.', 'type' => 'danger']);
            header('Location: /register');
            exit;
        }

        $pdo = \App\Framework\Database::pdo();
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE Email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            \App\Framework\TempData::start();
            \App\Framework\TempData::set('flash', ['message' => 'An account with that email already exists.', 'type' => 'danger']);
            header('Location: /register');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (role, name, Email, password_hash, is_blocked, created_at, updated_at) VALUES (?, ?, ?, ?, 0, NOW(), NOW())');
        $ok = $ins->execute(['member', $name, $email, $hash]);

        \App\Framework\TempData::start();
        if ($ok) {
            \App\Framework\TempData::set('flash', ['message' => 'Account created. Please login.', 'type' => 'success']);
        } else {
            \App\Framework\TempData::set('flash', ['message' => 'Registration failed. Please try again.', 'type' => 'danger']);
        }

        header('Location: /login');
        exit;
    });
    $r->addRoute('GET', '/logout', function() { (new AuthController())->logout(); });

    // Loan and reservation POST routes
    $r->addRoute('POST', '/loan/borrow', function() { (new LoanController())->borrow(); });
    $r->addRoute('POST', '/loan/return', function() { (new LoanController())->returnBook(); });
    $r->addRoute('POST', '/reserve', function() { (new ReservationController())->reserve(); });
    $r->addRoute('POST', '/reserve/cancel', function() { (new ReservationController())->cancel(); });
    $r->addRoute('POST', '/admin/reservation/process', function() { (new AdminController())->processReservation(); });

    // Member dashboard routes (login required)
    $r->addRoute('GET', '/dashboard', function() { (new DashboardController())->index(); });
    $r->addRoute('GET', '/dashboard/loans', function() {
        Auth::requireLogin();
        $user = Auth::user();
        $ls = new LoanService();
        render('MemberDashboard/loans.php', ['loans' => $ls->getMyLoans((int)$user['id'])]);
    });
    $r->addRoute('GET', '/dashboard/reservation', function() {
        Auth::requireLogin();
        $user = Auth::user();
        $rs = new ReservationService();
        render('MemberDashboard/reservation.php', ['reservations' => $rs->getMyReservations((int)$user['id'])]);
    });

    // Shortcut routes used by navbar links
    $r->addRoute('GET', '/loans', function() {
        Auth::requireLogin();
        $user = Auth::user();
        $ls = new LoanService();
        render('MemberDashboard/loans.php', ['loans' => $ls->getMyLoans((int)$user['id'])]);
    });
    $r->addRoute('GET', '/reservations', function() {
        Auth::requireLogin();
        $user = Auth::user();
        $rs = new ReservationService();
        render('MemberDashboard/reservation.php', ['reservations' => $rs->getMyReservations((int)$user['id'])]);
    });
    $r->addRoute('GET', '/notifications', function() {
        Auth::requireLogin();
        render('alerts.php');
    });
    $r->addRoute('GET', '/alerts', function() {
        Auth::requireLogin();
        render('alerts.php');
    });
    $r->addRoute('GET', '/settings', function() { Auth::requireLogin(); render('MemberDashboard/settings.php'); });

    // Member profile edit routes
    $r->addRoute('GET', '/profile/edit', function() {
        Auth::requireLogin();
        $user = Auth::user();
        render('MemberDashboard/edit_profile.php', ['user' => $user]);
    });

    $r->addRoute('POST', '/profile/edit', function() {
        Auth::requireLogin();
        $user = Auth::user();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        \App\Framework\TempData::start();

        if ($name === '' || $email === '') {
            \App\Framework\TempData::set('flash', ['message' => 'Name and email are required.', 'type' => 'danger']);
            header('Location: /profile/edit');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \App\Framework\TempData::set('flash', ['message' => 'Invalid email address.', 'type' => 'danger']);
            header('Location: /profile/edit');
            exit;
        }

        $pdo = \App\Framework\Database::pdo();
        // Make sure email is not used by someone else
        $stmt = $pdo->prepare('SELECT id FROM users WHERE Email = ? AND id != ?');
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            \App\Framework\TempData::set('flash', ['message' => 'That email is already in use.', 'type' => 'danger']);
            header('Location: /profile/edit');
            exit;
        }

        $upd = $pdo->prepare('UPDATE users SET name = ?, Email = ?, updated_at = NOW() WHERE id = ?');
        $ok = $upd->execute([$name, $email, $user['id']]);

        if ($ok) {
            \App\Framework\TempData::set('flash', ['message' => 'Profile updated.', 'type' => 'success']);
            // Update user data in session if session is active
            if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['Email'] = $email;
                $_SESSION['user']['email'] = $email;
            }
            header('Location: /settings');
            exit;
        }

        \App\Framework\TempData::set('flash', ['message' => 'Failed to update profile.', 'type' => 'danger']);
        header('Location: /profile/edit');
        exit;
    });

    // Admin routes
    $r->addRoute('GET', '/admin/dashboard', function() { (new AdminController())->dashboard(); });
    $r->addRoute('GET', '/admin/books', function() { (new AdminController())->books(); });
    $r->addRoute('GET', '/admin/books/create', function() { (new AdminController())->showCreateForm(); });
    $r->addRoute('GET', '/admin/books/edit', function() { (new AdminController())->showEditForm(); });

    $r->addRoute('GET', '/admin/loans', function() { (new AdminController())->loansPage(); });
    $r->addRoute('GET', '/admin/loans/show', function() { (new AdminController())->loanDetailPage(); });
    $r->addRoute('GET', '/loan/detail', function() { (new AdminController())->loanDetailPage(); });
    $r->addRoute('GET', '/admin/reservation', function() { (new AdminController())->reservationsPage(); });
    $r->addRoute('GET', '/admin/settings', function() {
        Auth::requireLibrarian();
        render('MemberDashboard/settings.php');
    });

});

$httpMethod = $_SERVER['REQUEST_METHOD'];

// Legacy support: convert old query-style routes into modern paths
$uri = strtok($_SERVER['REQUEST_URI'], '?');
if (isset($_GET['route'])) {
    $legacy = trim($_GET['route'], '/');
    // Map common old routes
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
        $uri = '/books/' . (int)$_GET['id'];
    } elseif ($legacy === 'book/detail' && isset($_GET['book_id'])) {
        $uri = '/books/' . (int)$_GET['book_id'];
    } elseif ($legacy === 'catalog' && isset($_GET['q'])) {
        $uri = '/catalog';
    } else {
        // Fallback: use the route value as a path
        $uri = '/' . $legacy;
    }
}


$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 - Method Not Allowed";
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2] ?? [];
        echo $handler($vars);
        break;
}
