<?php

namespace App\ViewModels;

class NotificationsViewModel
{
    public string $title;
    public array $notifications;
    public int $count;

    public function __construct(string $title, array $notifications)
    {
        $this->title = $title;
        $this->notifications = $notifications;
        $this->count = count($notifications);
    }

    public static function fromNotifications(string $title, array $notifications): self
    {
        $mappedNotifications = [];

        foreach ($notifications as $notification) {
            $mappedNotifications[] = self::mapNotification($notification);
        }

        return new self($title, $mappedNotifications);
    }

    private static function mapNotification(array $notification): array
    {
        $type = strtolower(trim((string) ($notification['type'] ?? 'info')));

        return [
            'label' => trim((string) ($notification['label'] ?? 'Notification')),
            'message' => trim((string) ($notification['message'] ?? '')),
            'date' => self::formatDate($notification['date'] ?? ''),
            'type' => $type,
            'badgeClass' => self::badgeClass($type),
            'link' => trim((string) ($notification['link'] ?? '')),
        ];
    }

    private static function formatDate($value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d H:i');
        } catch (\Throwable) {
            return $value;
        }
    }

    private static function badgeClass(string $type): string
    {
        if ($type === 'danger') {
            return 'danger';
        }

        if ($type === 'warning') {
            return 'warning';
        }

        if ($type === 'success') {
            return 'success';
        }

        return 'secondary';
    }
}
