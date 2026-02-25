<?php

namespace App\Services;

use App\Enum\ReservationStatus;
use App\Repository\IReservationRepository;
use App\Repository\ReservationRepository;

class ReservationService implements IReservationService
{
    private IReservationRepository $reservations;

    public function __construct(?IReservationRepository $reservations = null)
    {
        $this->reservations = $reservations ?? new ReservationRepository();
    }

    public function getMyReservations(int $userId): array
    {
        return $this->reservations->getByUser($userId);
    }

    public function getRecentForAdminDashboard(): array
    {
        return $this->reservations->getRecentWaitingWithBooks(3);
    }

    public function getWaitingCount(): int
    {
        return $this->reservations->countWaiting();
    }

    public function getAllActiveReservations(): array
    {
        return $this->reservations->getAllActiveWithDetails();
    }

    public function reserve(int $userId, int $bookId): bool
    {
        if ($this->reservations->hasActiveReservation($userId, $bookId)) {
            return false;
        }

        $newId = $this->reservations->create(
            $bookId,
            $userId,
            ReservationStatus::PENDING->value
        );

        return $newId > 0;
    }

    public function cancel(int $reservationId, int $userId): bool
    {
        return $this->reservations->cancel($reservationId, $userId);
    }

    public function markAsReady(int $reservationId): bool
    {
        if ($reservationId <= 0) {
            return false;
        }

        return $this->reservations->markAsReady($reservationId);
    }
}
