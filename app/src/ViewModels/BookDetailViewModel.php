<?php

namespace App\ViewModels;

class BookDetailViewModel
{
    public string $title;
    public array $book;
    public bool $isLoggedIn;
    public string $userRole;

    public function __construct(string $title, array $book, bool $isLoggedIn, string $userRole = '')
    {
        $this->title = $title;
        $this->book = $book;
        $this->isLoggedIn = $isLoggedIn;
        $this->userRole = $userRole;
    }

    public static function fromBook(array $book, bool $isLoggedIn, string $userRole = ''): self
    {
        $title = trim((string) ($book['Title'] ?? $book['title'] ?? ''));
        $author = trim((string) ($book['author'] ?? $book['Author'] ?? ''));
        $description = trim((string) ($book['Description'] ?? $book['description'] ?? ''));
        $availableCopies = self::readNullableInt($book, ['available_copies', 'available']);
        $totalCopies = self::readNullableInt($book, ['total_copies']);

        if ($title === '') {
            $title = 'Book';
        }

        if ($author === '') {
            $author = 'Unknown';
        }

        if ($description === '') {
            $description = 'No description available.';
        }

        $mappedBook = [
            'id' => (int) ($book['id'] ?? 0),
            'title' => $title,
            'author' => $author,
            'genre' => trim((string) ($book['Genre'] ?? $book['genre'] ?? '')),
            'isbn' => trim((string) ($book['ISBN'] ?? $book['isbn'] ?? '')),
            'publishedYear' => trim((string) ($book['published_year'] ?? $book['publishedYear'] ?? '')),
            'description' => $description,
            'availableCopies' => $availableCopies,
            'totalCopies' => $totalCopies,
            'coverPath' => self::coverPath((string) ($book['cover_url'] ?? $book['cover'] ?? '')),
        ];

        return new self($mappedBook['title'], $mappedBook, $isLoggedIn, $userRole);
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

    private static function readNullableInt(array $data, array $keys): ?int
    {
        foreach ($keys as $key) {
            if (!isset($data[$key]) || $data[$key] === '') {
                continue;
            }

            return (int) $data[$key];
        }

        return null;
    }
}
