<?php

namespace App\Models;

use App\Enum\ReservationStatus;

class Reservation
{
    public int $id;
    public int $book_id;
    public int $user_id;
    public string $status;
    public ReservationStatus $status_enum;
    public string $status_label;
    public string $book_title;
    public string $user_name;
    public ?string $created_at;
    public ?string $ready_at;
    public ?string $expires_at;

    public static function fromArray(array $row): self
    {
        $reservation = new self();

        $reservation->id = (int)($row['id'] ?? 0);
        $reservation->book_id = (int)($row['book_id'] ?? 0);
        $reservation->user_id = (int)($row['user_id'] ?? 0);

        $rawStatus = (string)($row['Status'] ?? $row['status'] ?? ReservationStatus::PENDING->value);
        $reservation->status = strtolower(trim($rawStatus));
        $reservation->status_enum = ReservationStatus::tryFrom($reservation->status) ?? ReservationStatus::PENDING;

        $reservation->book_title = (string)($row['Title'] ?? $row['book_title'] ?? '');
        $reservation->user_name = (string)($row['user_name'] ?? $row['name'] ?? '');

        $reservation->created_at = isset($row['created_at']) ? (string)$row['created_at'] : null;
        $reservation->ready_at = isset($row['ready_at']) ? (string)$row['ready_at'] : null;
        $reservation->expires_at = isset($row['expires_at']) ? (string)$row['expires_at'] : null;

        if ($reservation->status === ReservationStatus::PENDING->value) {
            $reservation->status_label = 'pending';
        } elseif ($reservation->status === ReservationStatus::APPROVED->value) {
            $reservation->status_label = 'ready';
        } else {
            $reservation->status_label = $reservation->status;
        }

        return $reservation;
    }
}
