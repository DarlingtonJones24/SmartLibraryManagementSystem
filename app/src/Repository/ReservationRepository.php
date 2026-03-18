<?php

namespace App\Repository;

use App\Enum\ReservationStatus;
use App\Framework\BaseRepository;

class ReservationRepository extends BaseRepository implements IReservationRepository
{
    public function createReservation(int $bookId, int $userId, string $status): int
    {
        $ok = $this->execute(
            "INSERT INTO reservations (book_id, user_id, Status, created_at)
             VALUES (?, ?, ?, NOW())",
            [$bookId, $userId, $status]
        );

        if (!$ok) {
            try {
                error_log(sprintf('[ReservationRepository::createReservation] INSERT failed for user=%d book=%d status=%s', $userId, $bookId, $status));
            } catch (\Throwable $_) {}
        }

        return $this->lastInsertId();
    }

    public function findActiveReservationsByUser(int $userId): array
    {
        return $this->fetchAll(
            "SELECT r.*, b.Title, b.author, b.cover_url
             FROM reservations r
             JOIN books b ON b.id = r.book_id
             WHERE r.user_id = ?
               AND r.Status IN (?, ?)
             ORDER BY r.created_at DESC",
            [
                $userId,
                ReservationStatus::PENDING->value,
                ReservationStatus::APPROVED->value
            ]
        );
    }

    public function findRecentPendingReservationsWithBooks(int $limit): array
    {
        $sql = "SELECT r.*, b.Title, b.cover_url,
                       DATE_ADD(r.created_at, INTERVAL 7 DAY) AS expires_at
                FROM reservations r
                LEFT JOIN books b ON b.id = r.book_id
                WHERE LOWER(COALESCE(r.Status, r.status, '')) = 'waiting'
                ORDER BY r.created_at ASC
                LIMIT " . (int)$limit;

        return $this->fetchAll($sql);
    }

    public function countPendingReservations(): int
    {
        $row = $this->fetchOne(
            "SELECT COUNT(*) as cnt
             FROM reservations
             WHERE LOWER(COALESCE(Status, status, '')) = 'waiting'"
        );

        return (int)($row['cnt'] ?? 0);
    }

    public function findAllActiveReservationsWithDetails(): array
    {
        return $this->fetchAll(
            "SELECT r.*, b.Title, b.author, b.cover_url, u.name as user_name
             FROM reservations r
             JOIN books b ON b.id = r.book_id
             JOIN users u ON u.id = r.user_id
             WHERE LOWER(COALESCE(r.Status, r.status, '')) <> 'canceled'
             ORDER BY r.created_at DESC"
        );
    }

    public function hasOpenReservation(int $userId, int $bookId): bool
    {
        $row = $this->fetchOne(
            "SELECT id
             FROM reservations
             WHERE user_id = ?
               AND book_id = ?
               AND Status IN ('waiting','ready')
             LIMIT 1",
            [$userId, $bookId]
        );

        return $row !== null;
    }

    public function cancelReservation(int $reservationId, int $userId): bool
    {
        return $this->execute(
            "UPDATE reservations SET Status = ? WHERE id = ? AND user_id = ?",
            [ReservationStatus::CANCELED->value, $reservationId, $userId]
        );
    }

    public function markReservationAsReady(int $reservationId): bool
    {
        return $this->execute(
            "UPDATE reservations
             SET Status = ?,
                 ready_at = NOW(),
                 expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY)
             WHERE id = ?
               AND LOWER(COALESCE(Status, status, '')) = ?
               AND COALESCE(expires_at, DATE_ADD(created_at, INTERVAL 7 DAY)) >= NOW()",
            [ReservationStatus::APPROVED->value, $reservationId, ReservationStatus::PENDING->value]
        );
    }

    public function findReservedBooksByUser(int $userId): array
    {
        return $this->fetchAll(
            "SELECT b.*, 0 AS available_copies, r.Status, r.created_at
             FROM reservations r
             JOIN books b ON b.id = r.book_id
             WHERE r.user_id = ?
               AND r.Status IN (?, ?)
             ORDER BY r.created_at DESC",
            [
                $userId,
                ReservationStatus::PENDING->value,
                ReservationStatus::APPROVED->value,
            ]
        );
    }
}
