<?php

namespace App\Repository;

interface IBookRepository
{
    public function getAll(
        string $search = '',
        string $filter = '',
        string $sort = 'title',
        string $direction = 'asc'
    ): array;

    public function findById(int $id): ?array;
    
    public function countAll(): int;

    public function countGenres(): int;

    public function countAvailable(): int;

    public function getTotalPhysicalCopiesCount(): int;
}