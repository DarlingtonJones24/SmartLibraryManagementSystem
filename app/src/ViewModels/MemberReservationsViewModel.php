<?php

namespace App\ViewModels;

class MemberReservationsViewModel
{
    public string $title;
    public array $reservations;

    public function __construct(string $title, array $reservations)
    {
        $this->title = $title;
        $this->reservations = $reservations;
    }

    public static function fromReservations(string $title, array $reservations): self
    {
        $mappedReservations = [];

        foreach ($reservations as $reservation) {
            $mappedReservations[] = self::mapReservation($reservation);
        }

        return new self($title, $mappedReservations);
    }

    private static function mapReservation(array $reservation): array
    {
        $status = trim((string) ($reservation['Status'] ?? $reservation['status'] ?? ''));
        $statusInfo = self::reservationStatus($status, $reservation['expires_at'] ?? '');

        return [
            'id' => (int) ($reservation['id'] ?? 0),
            'bookId' => (int) ($reservation['book_id'] ?? $reservation['bookId'] ?? 0),
            'title' => trim((string) ($reservation['Title'] ?? $reservation['title'] ?? '')),
            'author' => trim((string) ($reservation['author'] ?? $reservation['Author'] ?? '')),
            'coverPath' => self::coverPath((string) ($reservation['cover_url'] ?? $reservation['cover'] ?? '')),
            'createdAt' => self::formatDate($reservation['created_at'] ?? ''),
            'expiresAt' => self::formatDate($reservation['expires_at'] ?? ''),
            'statusLabel' => $statusInfo['label'],
            'statusClass' => $statusInfo['badgeClass'],
            'canPickup' => $statusInfo['label'] === 'ready',
        ];
    }

    private static function reservationStatus(string $status, $expiresAt): array
    {
        $status = strtolower(trim($status));

        if ($status === 'canceled' || $status === 'cancelled') {
            return ['label' => 'canceled', 'badgeClass' => 'badge-secondary'];
        }

        if ($status === 'fulfilled') {
            return ['label' => 'fulfilled', 'badgeClass' => 'badge-primary'];
        }

        if ($status === 'expired') {
            return ['label' => 'expired', 'badgeClass' => 'badge-danger'];
        }

        if (self::isPast($expiresAt)) {
            return ['label' => 'expired', 'badgeClass' => 'badge-danger'];
        }

        if ($status === 'ready') {
            return ['label' => 'ready', 'badgeClass' => 'badge-success'];
        }

        return ['label' => 'pending', 'badgeClass' => 'badge-warning'];
    }

    private static function formatDate($value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d');
        } catch (\Throwable) {
            return $value;
        }
    }

    private static function isPast($value): bool
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        try {
            return new \DateTimeImmutable($value) < new \DateTimeImmutable();
        } catch (\Throwable) {
            return false;
        }
    }

    private static function coverPath(string $cover): string
    {
        $cover = trim($cover);

        if ($cover === '') {
            return '/assets/Uploads/covers/default-cover.svg';
        }

        if (strpos($cover, '/') === 0 || stripos($cover, 'assets/Uploads/') === 0) {
            return '/' . ltrim($cover, '/');
        }

        return '/assets/Uploads/covers/' . rawurlencode($cover);
    }
}
