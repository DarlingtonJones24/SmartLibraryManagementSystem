<?php

namespace App\Repository;

interface IReservationRepository
{
    public function create(int $bookId, int $userId, string $status): int;

    public function getByUser(int $userId): array;

    public function getRecentWaitingWithBooks(int $limit): array;

    public function countWaiting(): int;

    public function getAllActiveWithDetails(): array;

    public function hasActiveReservation(int $userId, int $bookId): bool;

    public function cancel(int $reservationId, int $userId): bool;

    public function markAsReady(int $reservationId): bool;
}
