<?php

namespace App\Services;

class NotificationService
{
    private ILoanService $loanService;
    private IReservationService $reservationService;

    public function __construct(
        ?ILoanService $loanService = null,
        ?IReservationService $reservationService = null
    ) {
        $this->loanService = $loanService ?? new LoanService();
        $this->reservationService = $reservationService ?? new ReservationService();
    }

    public function getNotificationsForUser(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $notifications = [];
        $loans = $this->loanService->getActiveLoansForUser($userId);
        $reservations = $this->reservationService->getActiveReservationsForUser($userId);

        foreach ($loans as $loan) {
            $notification = $this->buildLoanNotification($loan);

            if ($notification !== null) {
                $notifications[] = $notification;
            }
        }

        foreach ($reservations as $reservation) {
            $notification = $this->buildReservationNotification($reservation);

            if ($notification !== null) {
                $notifications[] = $notification;
            }
        }

        usort($notifications, [$this, 'compareNotifications']);

        return $notifications;
    }

    public function countNotificationsForUser(int $userId): int
    {
        return count($this->getNotificationsForUser($userId));
    }

    private function buildLoanNotification(array $loan): ?array
    {
        $title = trim((string) ($loan['Title'] ?? $loan['title'] ?? 'this book'));
        $dueAt = $this->createDate($loan['due_at'] ?? '');

        if ($dueAt === null) {
            return null;
        }

        $now = new \DateTimeImmutable();

        if ($dueAt < $now) {
            return [
                'type' => 'danger',
                'label' => 'Overdue loan',
                'message' => 'Your loan for "' . $title . '" is overdue.',
                'date' => $dueAt->format('Y-m-d H:i:s'),
                'link' => '/loans',
                'sortPriority' => 3,
            ];
        }

        $daysLeft = (int) $now->diff($dueAt)->format('%r%a');

        if ($daysLeft <= 3) {
            return [
                'type' => 'warning',
                'label' => 'Due soon',
                'message' => 'Your loan for "' . $title . '" is due on ' . $dueAt->format('Y-m-d H:i') . '.',
                'date' => $dueAt->format('Y-m-d H:i:s'),
                'link' => '/loans',
                'sortPriority' => 2,
            ];
        }

        return null;
    }

    private function buildReservationNotification(array $reservation): ?array
    {
        $status = strtolower(trim((string) ($reservation['Status'] ?? $reservation['status'] ?? '')));

        if ($status !== 'ready') {
            return null;
        }

        $title = trim((string) ($reservation['Title'] ?? $reservation['title'] ?? 'this book'));
        $createdAt = $this->createDate($reservation['created_at'] ?? '');

        return [
            'type' => 'success',
            'label' => 'Reservation ready',
            'message' => 'Your reservation for "' . $title . '" is ready for pickup.',
            'date' => $createdAt?->format('Y-m-d H:i:s') ?? '',
            'link' => '/reservations',
            'sortPriority' => 1,
        ];
    }

    private function compareNotifications(array $left, array $right): int
    {
        $priorityCompare = ($right['sortPriority'] ?? 0) <=> ($left['sortPriority'] ?? 0);

        if ($priorityCompare !== 0) {
            return $priorityCompare;
        }

        return strcmp((string) ($right['date'] ?? ''), (string) ($left['date'] ?? ''));
    }

    private function createDate($value): ?\DateTimeImmutable
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
