<?php

namespace App\Services;

interface IBookService
{
    public function getBooks(
        string $search = '',
        string $filter = '',
        string $sort = 'title',
        string $direction = 'asc'
    ): array;

    public function getBook(int $id): ?array;

    public function createFullBookEntry(array $data): bool;

    public function updateBook(int $id, array $data): bool;

    public function deleteBook(int $id): bool;
}