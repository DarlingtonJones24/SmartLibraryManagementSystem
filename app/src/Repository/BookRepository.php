<?php

namespace App\Repository;

use App\Framework\BaseRepository;

class BookRepository extends BaseRepository implements IBookRepository
{
    public function getAll(
        string $search = '',
        string $filter = '',
        string $sort = 'title',
        string $direction = 'asc'
    ): array {
        $params = [];
        $where = [];

        if ($search !== '') {
            $where[] = "(b.Title LIKE ? OR b.author LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql = "SELECT b.*,
                (COALESCE(b.total_copies, 0) - 
                (SELECT COUNT(*) FROM loans l JOIN book_copies bc ON bc.id = l.copy_id WHERE bc.book_id = b.id AND l.returned_at IS NULL)) 
                AS available_copies
                FROM books b";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sortMap = [
            'title' => 'b.Title',
            'author' => 'b.author',
            'published' => 'b.published_year',
        ];

        $sortKey = strtolower(trim($sort));
        $orderBy = $sortMap[$sortKey] ?? 'b.Title';
        $dir = strtolower(trim($direction)) === 'desc' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY {$orderBy} {$dir}";

        return $this->fetchAll($sql, $params);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM books WHERE id = ?", [$id]);
    }

    public function countAll(): int
    {
        $row = $this->fetchOne("SELECT COUNT(*) as cnt FROM books");
        return (int)($row['cnt'] ?? 0);
    }

    public function countGenres(): int
    {
        $row = $this->fetchOne("SELECT COUNT(DISTINCT Genre) as cnt FROM books");
        return (int)($row['cnt'] ?? 0);
    }

    public function countAvailable(): int
    {
        $row = $this->fetchOne(
            "SELECT
                COALESCE(SUM(total_copies), 0)
                - COALESCE((
                    SELECT COUNT(*)
                    FROM loans l
                    WHERE l.returned_at IS NULL
                ), 0) AS cnt
             FROM books"
        );

        return max(0, (int)($row['cnt'] ?? 0));
    }

    public function getTotalPhysicalCopiesCount(): int
    {
        $row = $this->fetchOne("SELECT SUM(total_copies) as total FROM books");
        return (int)($row['total'] ?? 0);
    }
}