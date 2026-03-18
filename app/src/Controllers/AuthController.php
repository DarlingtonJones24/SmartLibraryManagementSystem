<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\IAuthService;
use App\Services\AuthService;

class AuthController extends Controller
{
    private IAuthService $authService;

    public function __construct(?IAuthService $authService = null)
    {
        parent::__construct();
        $this->authService = $authService ?? new AuthService();
    }

    public function showLoginForm(): void
    {
        $this->render('Auth/login', [
            'title' => 'Login',
            'error' => null,
            'hideSidebar' => false
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->render('Auth/login', [
                'title' => 'Login',
                'error' => 'Email and password are required.',
                'hideSidebar' => false
            ]);
            return;
        }

        $user = $this->authService->login($email, $password);

        if (!$user) {
            $this->render('Auth/login', [
                'title' => 'Login',
                'error' => 'Invalid login or account blocked.',
                'hideSidebar' => false
            ]);
            return;
        }

        Auth::login($user);
        $this->setMessage('Welcome back!', 'success');
        if (($user['role'] ?? '') === 'librarian') {
            $this->redirect('admin/dashboard');
            return;
        }

        $this->redirect('dashboard');
    }

    public function showRegisterForm(): void
    {
        $this->render('Auth/register', ['title' => 'Register']);
    }

    public function register(): void
    {
        $result = $this->authService->registerMember(
            (string) ($_POST['name'] ?? ''),
            (string) ($_POST['email'] ?? ''),
            (string) ($_POST['password'] ?? ''),
            (string) ($_POST['password2'] ?? '')
        );

        $this->setMessage($result['message'], $result['success'] ? 'success' : 'danger');
        $this->redirect($result['success'] ? 'login' : 'register');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->setMessage('You have been logged out.', 'success');
        $this->redirect('login');
    }
}
