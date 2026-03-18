<?php

namespace App\ViewModels;

class AdminLoanDetailViewModel
{
    public string $title;
    public array $loan;

    public function __construct(string $title, array $loan)
    {
        $this->title = $title;
        $this->loan = $loan;
    }

    public static function fromLoan(array $loan): self
    {
        $title = trim((string) ($loan['Title'] ?? $loan['title'] ?? ''));

        if ($title === '') {
            $title = 'Loan Details';
        }

        $mappedLoan = [
            'id' => (int) ($loan['id'] ?? 0),
            'title' => $title,
            'author' => trim((string) ($loan['author'] ?? $loan['Author'] ?? 'Unknown')),
            'genre' => trim((string) ($loan['Genre'] ?? $loan['genre'] ?? '')),
            'isbn' => trim((string) ($loan['ISBN'] ?? $loan['isbn'] ?? '')),
            'publishedYear' => trim((string) ($loan['published_year'] ?? $loan['publishedYear'] ?? '')),
            'description' => trim((string) ($loan['Description'] ?? $loan['description'] ?? '')),
            'loanedAt' => self::formatDate($loan['loaned_at'] ?? ''),
            'dueAt' => self::formatDate($loan['due_at'] ?? ''),
            'returnedAt' => self::formatDate($loan['returned_at'] ?? ''),
            'coverPath' => self::coverPath((string) ($loan['cover_url'] ?? $loan['cover'] ?? '')),
            'userName' => trim((string) ($loan['user_name'] ?? $loan['userName'] ?? $loan['user_id'] ?? '')),
            'userEmail' => trim((string) ($loan['user_email'] ?? $loan['userEmail'] ?? '')),
        ];

        if ($mappedLoan['description'] === '') {
            $mappedLoan['description'] = 'No description available.';
        }

        $title = $mappedLoan['title'] !== '' ? $mappedLoan['title'] : 'Loan Details';

        return new self($title, $mappedLoan);
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
