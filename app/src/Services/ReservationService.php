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

    public function getActiveReservationsForUser(int $userId): array
    {
        return $this->reservations->findActiveReservationsByUser($userId);
    }

    public function getRecentPendingReservations(int $limit = 3): array
    {
        return $this->reservations->findRecentPendingReservationsWithBooks($limit);
    }

    public function countPendingReservations(): int
    {
        return $this->reservations->countPendingReservations();
    }

    public function getActiveReservationsForAdmin(): array
    {
        return $this->reservations->findAllActiveReservationsWithDetails();
    }

    public function createReservation(int $userId, int $bookId): bool
    {
        if ($this->reservations->hasOpenReservation($userId, $bookId)) {
            return false;
        }

        $newId = $this->reservations->createReservation(
            $bookId,
            $userId,
            ReservationStatus::PENDING->value
        );

        return $newId > 0;
    }

    public function cancelReservation(int $reservationId, int $userId): bool
    {
        return $this->reservations->cancelReservation($reservationId, $userId);
    }

    public function markReservationReady(int $reservationId): bool
    {
        if ($reservationId <= 0) {
            return false;
        }

        return $this->reservations->markReservationAsReady($reservationId);
    }
}
