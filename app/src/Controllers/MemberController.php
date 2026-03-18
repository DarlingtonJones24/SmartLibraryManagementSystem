<?php

namespace App\Controllers;

use App\Framework\Auth;
use App\Framework\Controller;
use App\Services\ILoanService;
use App\Services\IReservationService;
use App\Services\LoanService;
use App\Services\NotificationService;
use App\Services\ReservationService;
use App\Services\UserService;
use App\ViewModels\MemberLoansViewModel;
use App\ViewModels\MemberReservationsViewModel;
use App\ViewModels\NotificationsViewModel;
use App\ViewModels\ProfileViewModel;

class MemberController extends Controller
{
    private ILoanService $loanService;
    private IReservationService $reservationService;
    private NotificationService $notificationService;
    private UserService $userService;

    public function __construct(
        ?ILoanService $loanService = null,
        ?IReservationService $reservationService = null,
        ?NotificationService $notificationService = null,
        ?UserService $userService = null
    ) {
        parent::__construct();
        $this->loanService = $loanService ?? new LoanService();
        $this->reservationService = $reservationService ?? new ReservationService();
        $this->notificationService = $notificationService ?? new NotificationService();
        $this->userService = $userService ?? new UserService();
    }

    public function showLoansPage(): void
    {
        Auth::requireLogin();
        $user = $this->userService->normalizeSessionUser(Auth::user());
        $viewModel = MemberLoansViewModel::fromLoans(
            'My Loans',
            $this->loanService->getActiveLoansForUser((int) $user['id'])
        );

        $this->render('MemberDashboard/loans', [
            'title' => $viewModel->title,
            'memberLoansViewModel' => $viewModel,
        ]);
    }

    public function showReservationsPage(): void
    {
        Auth::requireLogin();
        $user = $this->userService->normalizeSessionUser(Auth::user());
        $viewModel = MemberReservationsViewModel::fromReservations(
            'My Reservations',
            $this->reservationService->getActiveReservationsForUser((int) $user['id'])
        );

        $this->render('MemberDashboard/reservation', [
            'title' => $viewModel->title,
            'memberReservationsViewModel' => $viewModel,
        ]);
    }

    public function showSettingsPage(): void
    {
        Auth::requireLogin();
        $user = $this->userService->normalizeSessionUser(Auth::user());
        $viewModel = ProfileViewModel::fromUser('Account Settings', $user);

        $this->render('MemberDashboard/settings', [
            'title' => $viewModel->title,
            'profileViewModel' => $viewModel,
        ]);
    }

    public function showEditProfileForm(): void
    {
        Auth::requireLogin();
        $user = $this->userService->normalizeSessionUser(Auth::user());
        $viewModel = ProfileViewModel::fromUser('Edit Profile', $user);

        $this->render('MemberDashboard/edit_profile', [
            'title' => $viewModel->title,
            'profileViewModel' => $viewModel,
        ]);
    }

    public function updateProfile(): void
    {
        Auth::requireLogin();
        $user = $this->userService->normalizeSessionUser(Auth::user());
        $result = $this->userService->updateProfile(
            (int) $user['id'],
            (string) ($_POST['name'] ?? ''),
            (string) ($_POST['email'] ?? '')
        );

        $this->setMessage($result['message'], $result['success'] ? 'success' : 'danger');

        if ($result['success']) {
            Auth::login([
                'id' => $user['id'],
                'name' => trim((string) ($_POST['name'] ?? '')),
                'email' => trim((string) ($_POST['email'] ?? '')),
                'role' => $user['role'],
            ]);
            $this->redirect('settings');
            return;
        }

        $this->redirect('profile/edit');
    }

    public function showNotificationsPage(): void
    {
        Auth::requireLogin();
        $user = $this->userService->normalizeSessionUser(Auth::user());

        try {
            $notifications = $this->notificationService->getNotificationsForUser((int) $user['id']);
        } catch (\Throwable) {
            $notifications = [];
            $this->setMessage('Unable to load notifications right now.', 'danger');
        }

        $viewModel = NotificationsViewModel::fromNotifications('Notifications', $notifications);

        $this->render('alerts', [
            'title' => $viewModel->title,
            'notificationsViewModel' => $viewModel,
        ]);
    }
}
