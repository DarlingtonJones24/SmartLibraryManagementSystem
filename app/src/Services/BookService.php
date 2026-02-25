<?php

namespace App\Services;

use App\Repository\BookRepository;
use App\Repository\IBookRepository;
use App\Framework\Database;

class BookService implements IBookService
{
    private IBookRepository $books;

    public function __construct(?IBookRepository $books = null)
    {
        $this->books = $books ?? new BookRepository();
    }

    public function getBooks(string $search = '', string $filter = '', string $sort = 'title', string $direction = 'asc'): array
    {
        return $this->books->getAll($search, $filter, $sort, $direction);
    }

    public function getBook(int $id): ?array
    {
        return $this->books->findById($id);
    }

    public function createFullBookEntry(array $data): bool
    {
        $pdo = null;
        try {
            $pdo = Database::pdo();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('INSERT INTO books (Title, author, ISBN, Genre, published_year, cover_url, Description, total_copies, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([
                $data['Title'], 
                $data['author'], 
                $data['ISBN'], 
                $data['Genre'], 
                $data['published_year'], 
                $data['cover_url'], 
                $data['Description'],
                (int)$data['total_copies']
            ]);

            $bookId = (int)$pdo->lastInsertId();
            $copiesCount = (int)$data['total_copies'];

            if ($bookId > 0 && $copiesCount > 0) {
                $copyStmt = $pdo->prepare('INSERT INTO book_copies (book_id, copy_code, created_at) VALUES (?, ?, NOW())');
                for ($i = 0; $i < $copiesCount; $i++) {
                    $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                    $copyStmt->execute([$bookId, $code]);
                }
            }

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            if ($pdo !== null && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('[BookService] Create failed: ' . $e->getMessage());
            return false;
        }
    }

    public function updateBook(int $id, array $data): bool
    {
        try {
            $pdo = Database::pdo();
            $stmt = $pdo->prepare('UPDATE books SET Title = ?, author = ?, ISBN = ?, Genre = ?, published_year = ?, cover_url = ?, Description = ? WHERE id = ?');
            return $stmt->execute([
                $data['Title'], $data['author'], $data['ISBN'], 
                $data['Genre'], $data['published_year'], 
                $data['cover_url'], $data['Description'], $id
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function deleteBook(int $id): bool
    {
        try {
            $pdo = Database::pdo();
            return $pdo->prepare('DELETE FROM books WHERE id = ?')->execute([$id]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}