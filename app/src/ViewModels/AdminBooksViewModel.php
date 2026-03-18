<?php

namespace App\ViewModels;

class AdminBooksViewModel
{
    public string $title;
    public string $searchQuery;
    public string $sort;
    public string $direction;
    public array $books;

    public function __construct(string $title, string $searchQuery, string $sort, string $direction, array $books)
    {
        $this->title = $title;
        $this->searchQuery = $searchQuery;
        $this->sort = $sort;
        $this->direction = $direction;
        $this->books = $books;
    }

    public static function fromBooks(string $title, string $searchQuery, string $sort, string $direction, array $books): self
    {
        $mappedBooks = [];

        foreach ($books as $book) {
            $mappedBooks[] = self::mapBook($book);
        }

        return new self(
            $title,
            $searchQuery,
            $sort,
            $direction,
            $mappedBooks
        );
    }

    private static function mapBook(array $book): array
    {
        $availableCopies = self::readNullableInt($book, ['available_copies', 'available']);
        $totalCopies = self::readNullableInt($book, ['total_copies']);

        return [
            'id' => self::readInt($book, ['id']),
            'title' => self::readString($book, ['Title', 'title']),
            'author' => self::readString($book, ['author', 'Author']),
            'genre' => self::readString($book, ['Genre', 'genre']),
            'publishedYear' => self::readString($book, ['published_year', 'publishedYear']),
            'isbn' => self::readString($book, ['ISBN', 'isbn']),
            'coverPath' => self::coverPath(self::readString($book, ['cover_url', 'cover'])),
            'availableCopies' => $availableCopies,
            'totalCopies' => $totalCopies,
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

    private static function readString(array $data, array $keys): string
    {
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                continue;
            }

            return trim((string) $data[$key]);
        }

        return '';
    }

    private static function readInt(array $data, array $keys): int
    {
        foreach ($keys as $key) {
            if (!isset($data[$key]) || $data[$key] === '') {
                continue;
            }

            return (int) $data[$key];
        }

        return 0;
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
