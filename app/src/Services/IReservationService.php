<?php

namespace App\Services;

interface IReservationService
{
    public function getActiveReservationsForUser(int $userId): array;

    public function getRecentPendingReservations(int $limit = 3): array;

    public function countPendingReservations(): int;

    public function getActiveReservationsForAdmin(): array;

    public function createReservation(int $userId, int $bookId): bool;

    public function cancelReservation(int $reservationId, int $userId): bool;

    public function markReservationReady(int $reservationId): bool;
}
