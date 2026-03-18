<?php

namespace App\Repository;

use App\Framework\BaseRepository;

class BookRepository extends BaseRepository implements IBookRepository
{
    public function findCatalogBooks(
        string $search = '',
        string $sort = 'title',
        string $direction = 'asc',
        bool $onlyAvailable = false
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
                       GREATEST(
                           COALESCE(b.total_copies, 0) - (
                               SELECT COUNT(*)
                               FROM loans l
                               JOIN book_copies bc ON bc.id = l.copy_id
                               WHERE bc.book_id = b.id
                                 AND l.returned_at IS NULL
                           ),
                           0
                       ) AS available_copies
                FROM books b";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        if ($onlyAvailable) {
            $sql .= " HAVING available_copies > 0";
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

    public function findBookDetails(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT b.*,
                    GREATEST(
                        COALESCE(b.total_copies, 0) - (
                            SELECT COUNT(*)
                            FROM loans l
                            JOIN book_copies bc ON bc.id = l.copy_id
                            WHERE bc.book_id = b.id
                              AND l.returned_at IS NULL
                        ),
                        0
                    ) AS available_copies
             FROM books b
             WHERE b.id = ?",
            [$id]
        );
    }

    public function createBookWithCopies(array $bookData): bool
    {
        try {
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare(
                'INSERT INTO books (Title, author, ISBN, Genre, published_year, cover_url, Description, total_copies, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())'
            );
            $statement->execute([
                $bookData['Title'],
                $bookData['author'],
                $bookData['ISBN'],
                $bookData['Genre'],
                $bookData['published_year'],
                $bookData['cover_url'],
                $bookData['Description'],
                $bookData['total_copies'],
            ]);

            $bookId = (int) $this->pdo->lastInsertId();
            $totalCopies = (int) ($bookData['total_copies'] ?? 0);

            if ($bookId > 0 && $totalCopies > 0) {
                $copyStatement = $this->pdo->prepare(
                    'INSERT INTO book_copies (book_id, copy_code, created_at) VALUES (?, ?, NOW())'
                );

                for ($index = 0; $index < $totalCopies; $index++) {
                    $copyStatement->execute([$bookId, $this->generateCopyCode()]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log('[BookRepository] Failed to create book with copies: ' . $exception->getMessage());
            return false;
        }
    }

    public function updateBookDetails(int $id, array $bookData): bool
    {
        try {
            $this->pdo->beginTransaction();

            $currentCopiesRow = $this->fetchOne(
                'SELECT COUNT(*) AS cnt FROM book_copies WHERE book_id = ?',
                [$id]
            );
            $currentCopies = (int) ($currentCopiesRow['cnt'] ?? 0);
            $targetCopies = (int) ($bookData['total_copies'] ?? 0);

            if ($targetCopies > $currentCopies) {
                $copiesToAdd = $targetCopies - $currentCopies;
                $copyStatement = $this->pdo->prepare(
                    'INSERT INTO book_copies (book_id, copy_code, created_at) VALUES (?, ?, NOW())'
                );

                for ($index = 0; $index < $copiesToAdd; $index++) {
                    $copyStatement->execute([$id, $this->generateCopyCode()]);
                }
            } elseif ($targetCopies < $currentCopies) {
                $copiesToRemove = $currentCopies - $targetCopies;
                $removableCopies = $this->fetchAll(
                    'SELECT bc.id
                     FROM book_copies bc
                     LEFT JOIN loans l ON l.copy_id = bc.id
                     WHERE bc.book_id = ?
                       AND l.id IS NULL
                     LIMIT ' . $copiesToRemove,
                    [$id]
                );

                if (count($removableCopies) < $copiesToRemove) {
                    $this->pdo->rollBack();
                    return false;
                }

                $copyIds = [];
                foreach ($removableCopies as $copy) {
                    $copyIds[] = (int) $copy['id'];
                }
                $placeholders = implode(', ', array_fill(0, count($copyIds), '?'));
                $deleteStatement = $this->pdo->prepare('DELETE FROM book_copies WHERE id IN (' . $placeholders . ')');
                $deleteStatement->execute($copyIds);
            }

            $updateStatement = $this->pdo->prepare(
                'UPDATE books
                 SET Title = ?, author = ?, ISBN = ?, Genre = ?, published_year = ?, cover_url = ?, Description = ?, total_copies = ?
                 WHERE id = ?'
            );
            $updated = $updateStatement->execute([
                $bookData['Title'],
                $bookData['author'],
                $bookData['ISBN'],
                $bookData['Genre'],
                $bookData['published_year'],
                $bookData['cover_url'],
                $bookData['Description'],
                $targetCopies,
                $id,
            ]);

            $this->pdo->commit();
            return $updated;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            error_log('[BookRepository] Failed to update book details: ' . $exception->getMessage());
            return false;
        }
    }

    public function deleteBook(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM books WHERE id = ?');
        $statement->execute([$id]);

        return $statement->rowCount() > 0;
    }

    public function hasLoanHistory(int $id): bool
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS cnt
             FROM loans l
             JOIN book_copies bc ON bc.id = l.copy_id
             WHERE bc.book_id = ?',
            [$id]
        );

        return (int) ($row['cnt'] ?? 0) > 0;
    }

    public function countBooks(): int
    {
        $row = $this->fetchOne('SELECT COUNT(*) AS cnt FROM books');
        return (int) ($row['cnt'] ?? 0);
    }

    public function countGenres(): int
    {
        $row = $this->fetchOne("SELECT COUNT(DISTINCT Genre) as cnt FROM books");
        return (int)($row['cnt'] ?? 0);
    }

    public function countAvailableCopies(): int
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

    public function countTotalCopies(): int
    {
        $row = $this->fetchOne("SELECT SUM(total_copies) as total FROM books");
        return (int)($row['total'] ?? 0);
    }

    private function generateCopyCode(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    }
}
