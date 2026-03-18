<?php

namespace App\ViewModels;

class CatalogViewModel
{
    public string $title;
    public string $searchQuery;
    public string $filter;
    public string $sort;
    public string $direction;
    public bool $isLoggedIn;
    public array $books;
    public int $currentPage;
    public int $totalPages;
    public array $pageItems;

    public function __construct(
        string $title,
        string $searchQuery,
        string $filter,
        string $sort,
        string $direction,
        bool $isLoggedIn,
        array $books,
        int $currentPage,
        int $totalPages,
        array $pageItems
    ) {
        $this->title = $title;
        $this->searchQuery = $searchQuery;
        $this->filter = $filter;
        $this->sort = $sort;
        $this->direction = $direction;
        $this->isLoggedIn = $isLoggedIn;
        $this->books = $books;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        $this->pageItems = $pageItems;
    }

    public static function fromBooks(
        string $title,
        string $searchQuery,
        string $filter,
        string $sort,
        string $direction,
        bool $isLoggedIn,
        array $books,
        int $currentPage = 1
    ): self {
        $mappedBooks = [];

        foreach ($books as $book) {
            $mappedBooks[] = self::mapBook($book);
        }

        $currentPage = max(1, $currentPage);
        $perPage = 4;

        if (count($mappedBooks) === 0) {
            $totalPages = 1;
        } else {
            $totalPages = (int) ceil(count($mappedBooks) / $perPage);
        }

        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        return new self(
            $title,
            $searchQuery,
            $filter,
            $sort,
            $direction,
            $isLoggedIn,
            $mappedBooks,
            $currentPage,
            $totalPages,
            array_slice($mappedBooks, $offset, $perPage)
        );
    }

    private static function mapBook(array $book): array
    {
        $availableCopies = self::readNullableInt($book, ['available_copies', 'available']);
        $totalCopies = self::readNullableInt($book, ['total_copies']);
        $canBorrow = true;
        $availabilityText = '';
        $availabilityClass = '';
        $showLowStock = false;

        if ($availableCopies !== null) {
            $canBorrow = $availableCopies > 0;
            $availabilityText = $availableCopies > 0 ? 'Available (' . $availableCopies . ')' : 'Unavailable';
            $availabilityClass = $availableCopies > 0 ? 'badge-available' : 'badge-unavailable';
            $showLowStock = $availableCopies > 0 && $availableCopies <= 2;
        } elseif ($totalCopies !== null) {
            $canBorrow = $totalCopies > 0;
            $availabilityText = $totalCopies > 0 ? 'Total copies: ' . $totalCopies : 'Unavailable';
            $availabilityClass = $totalCopies > 0 ? 'badge-info' : 'badge-unavailable';
            $showLowStock = $totalCopies > 0 && $totalCopies <= 2;
        }

        return [
            'id' => (int) ($book['id'] ?? 0),
            'title' => trim((string) ($book['Title'] ?? $book['title'] ?? '')),
            'author' => trim((string) ($book['author'] ?? $book['Author'] ?? '')),
            'genre' => trim((string) ($book['Genre'] ?? $book['genre'] ?? '')),
            'type' => trim((string) ($book['Type'] ?? $book['type'] ?? '')),
            'isbn' => trim((string) ($book['ISBN'] ?? $book['isbn'] ?? '')),
            'publishedYear' => trim((string) ($book['published_year'] ?? $book['publishedYear'] ?? '')),
            'coverPath' => self::coverPath((string) ($book['cover_url'] ?? $book['cover'] ?? '')),
            'availableCopies' => $availableCopies,
            'totalCopies' => $totalCopies,
            'canBorrow' => $canBorrow,
            'canReserve' => !$canBorrow,
            'availabilityText' => $availabilityText,
            'availabilityClass' => $availabilityClass,
            'showLowStock' => $showLowStock,
        ];
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
