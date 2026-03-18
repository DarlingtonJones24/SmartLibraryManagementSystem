<?php

namespace App\Repository;

interface IReservationRepository
{
    public function createReservation(int $bookId, int $userId, string $status): int;

    public function findActiveReservationsByUser(int $userId): array;

    public function findRecentPendingReservationsWithBooks(int $limit): array;

    public function countPendingReservations(): int;

    public function findAllActiveReservationsWithDetails(): array;

    public function hasOpenReservation(int $userId, int $bookId): bool;

    public function cancelReservation(int $reservationId, int $userId): bool;

    public function markReservationAsReady(int $reservationId): bool;

    public function findReservedBooksByUser(int $userId): array;
}
