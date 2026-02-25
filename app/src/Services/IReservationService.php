<?php

namespace App\Services;

interface IReservationService
{
    public function getMyReservations(int $userId): array;

    public function getRecentForAdminDashboard(): array;

    public function getWaitingCount(): int;

    public function getAllActiveReservations(): array;

    public function reserve(int $userId, int $bookId): bool;

    public function cancel(int $reservationId, int $userId): bool;

    public function markAsReady(int $reservationId): bool;
}
