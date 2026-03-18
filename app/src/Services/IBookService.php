<?php

namespace App\Services;

interface IBookService
{
    public function getCatalogBooks(
        string $search = '',
        string $filter = '',
        string $sort = 'title',
        string $direction = 'asc',
        ?int $userId = null
    ): array;

    public function getBookDetails(int $id): ?array;

    public function createBookWithCopies(array $data): bool;

    public function updateBookDetails(int $id, array $data): bool;

    public function deleteBook(int $id): array;

    public function countBooks(): int;

    public function countAvailableCopies(): int;

    public function countTotalCopies(): int;
}
