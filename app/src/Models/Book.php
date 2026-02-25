<?php

namespace App\Models;

class Book
{
    public int $id;
    public string $title;
    public string $author;
    public string $isbn;
    public string $genre;
    public string $description;
    public int $published_year;
    public string $cover_url;
    public int $total_copies;
    public int $available;
    public int $available_copies;
    public bool $is_available;
    public ?string $created_at;
    public ?string $updated_at;

    public static function fromArray(array $row): self
    {
        $book = new self();

        $book->id = (int)($row['id'] ?? 0);
        $book->title = (string)($row['Title'] ?? $row['title'] ?? '');
        $book->author = (string)($row['author'] ?? $row['Author'] ?? '');
        $book->isbn = (string)($row['ISBN'] ?? $row['isbn'] ?? '');
        $book->genre = (string)($row['Genre'] ?? $row['genre'] ?? '');
        $book->description = (string)($row['Description'] ?? $row['description'] ?? '');
        $book->published_year = (int)($row['published_year'] ?? $row['publishedYear'] ?? 0);
        $book->cover_url = (string)($row['cover_url'] ?? $row['cover'] ?? '');

        $book->total_copies = (int)($row['total_copies'] ?? $row['totalCopies'] ?? 0);
        $book->available = (int)($row['available'] ?? $row['available_count'] ?? $row['available_copies'] ?? 0);
        $book->available_copies = (int)($row['available_copies'] ?? $book->available);
        $book->is_available = $book->available > 0;

        $book->created_at = isset($row['created_at']) ? (string)$row['created_at'] : null;
        $book->updated_at = isset($row['updated_at']) ? (string)$row['updated_at'] : null;

        return $book;
    }

    public function isAvailable(): bool
    {
        return $this->available > 0 || $this->available_copies > 0;
    }
}
