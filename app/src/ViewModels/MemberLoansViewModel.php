<?php

namespace App\ViewModels;

class MemberLoansViewModel
{
    public string $title;
    public array $loans;

    public function __construct(string $title, array $loans)
    {
        $this->title = $title;
        $this->loans = $loans;
    }

    public static function fromLoans(string $title, array $loans): self
    {
        $mappedLoans = [];

        foreach ($loans as $loan) {
            $mappedLoans[] = self::mapLoan($loan);
        }

        return new self($title, $mappedLoans);
    }

    private static function mapLoan(array $loan): array
    {
        return [
            'id' => (int) ($loan['id'] ?? 0),
            'title' => trim((string) ($loan['Title'] ?? $loan['title'] ?? '')),
            'author' => trim((string) ($loan['author'] ?? $loan['Author'] ?? '')),
            'coverPath' => self::coverPath((string) ($loan['cover_url'] ?? $loan['cover'] ?? '')),
            'loanedAt' => self::formatDate($loan['loaned_at'] ?? ''),
            'dueAt' => self::formatDate($loan['due_at'] ?? ''),
            'isOverdue' => self::isPast($loan['due_at'] ?? ''),
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
