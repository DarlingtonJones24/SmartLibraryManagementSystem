<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->render('Auth/login', [
            'title' => 'Login',
            'error' => null,
            'hideSidebar' => false
        ]);
    }

    public function loginPost(): void
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

        $service = new AuthService();
        $user = $service->login($email, $password);

        if (!$user) {
            $this->render('Auth/login', [
                'title' => 'Login',
                'error' => 'Invalid login or account blocked.',
                'hideSidebar' => false
            ]);
            return;
        }

        Auth::login($user);

        $this->flash('Welcome back!', 'success');

        // Send librarians to admin dashboard, members to dashboard
        if (($user['role'] ?? '') === 'librarian') {
            $this->redirect('admin/dashboard');
            return;
        }

        $this->redirect('dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->flash('You have been logged out.', 'success');
        // Go to login after logout
        $this->redirect('login');
    }
}
