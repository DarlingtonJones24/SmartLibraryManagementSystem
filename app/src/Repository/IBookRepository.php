<?php

namespace App\Repository;

interface IBookRepository
{
    public function findCatalogBooks(
        string $search = '',
        string $sort = 'title',
        string $direction = 'asc',
        bool $onlyAvailable = false
    ): array;

    public function findBookDetails(int $id): ?array;

    public function createBookWithCopies(array $bookData): bool;

    public function updateBookDetails(int $id, array $bookData): bool;

    public function deleteBook(int $id): bool;

    public function hasLoanHistory(int $id): bool;

    public function countBooks(): int;

    public function countAvailableCopies(): int;

    public function countGenres(): int;

    public function countTotalCopies(): int;
}
