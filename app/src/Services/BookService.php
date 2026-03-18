<?php

namespace App\Services;

use App\Repository\BookRepository;
use App\Repository\IBookRepository;
use App\Repository\ILoanRepository;
use App\Repository\IReservationRepository;
use App\Repository\LoanRepository;
use App\Repository\ReservationRepository;

class BookService implements IBookService
{
    private IBookRepository $books;
    private ILoanRepository $loans;
    private IReservationRepository $reservations;

    public function __construct(
        ?IBookRepository $books = null,
        ?ILoanRepository $loans = null,
        ?IReservationRepository $reservations = null
    ) {
        $this->books = $books ?? new BookRepository();
        $this->loans = $loans ?? new LoanRepository();
        $this->reservations = $reservations ?? new ReservationRepository();
    }

    public function getCatalogBooks(
        string $search = '',
        string $filter = '',
        string $sort = 'title',
        string $direction = 'asc',
        ?int $userId = null
    ): array {
        $normalizedFilter = strtolower(trim($filter));

        if ($normalizedFilter === 'available') {
            return $this->books->findCatalogBooks($search, $sort, $direction, true);
        }

        if ($normalizedFilter === 'overdue') {
            if ($userId === null) {
                return $this->books->findCatalogBooks($search, $sort, $direction);
            }

            return $this->filterAndSortBooks(
                $this->loans->findOverdueBooksByUser($userId),
                $search,
                $sort,
                $direction
            );
        }

        if ($normalizedFilter === 'reserved') {
            if ($userId === null) {
                return $this->books->findCatalogBooks($search, $sort, $direction);
            }

            return $this->filterAndSortBooks(
                $this->reservations->findReservedBooksByUser($userId),
                $search,
                $sort,
                $direction
            );
        }

        return $this->books->findCatalogBooks($search, $sort, $direction);
    }

    public function getBookDetails(int $id): ?array
    {
        return $this->books->findBookDetails($id);
    }

    public function createBookWithCopies(array $data): bool
    {
        $bookData = $this->normalizeBookData($data);
        return $bookData['Title'] !== '' && $this->books->createBookWithCopies($bookData);
    }

    public function updateBookDetails(int $id, array $data): bool
    {
        if ($id <= 0) {
            return false;
        }

        $bookData = $this->normalizeBookData($data);

        if ($bookData['Title'] === '') {
            return false;
        }

        return $this->books->updateBookDetails($id, $bookData);
    }

    public function deleteBook(int $id): array
    {
        if ($id <= 0) {
            return ['success' => false, 'message' => 'Invalid book id.'];
        }

        if ($this->books->findBookDetails($id) === null) {
            return ['success' => false, 'message' => 'Book not found.'];
        }

        if ($this->books->hasLoanHistory($id)) {
            return [
                'success' => false,
                'message' => 'This book cannot be deleted because it has loan history. Edit it instead.',
            ];
        }

        $deleted = $this->books->deleteBook($id);

        return [
            'success' => $deleted,
            'message' => $deleted ? 'Book deleted.' : 'Failed to delete book.',
        ];
    }

    public function countBooks(): int
    {
        return $this->books->countBooks();
    }

    public function countAvailableCopies(): int
    {
        return $this->books->countAvailableCopies();
    }

    public function countTotalCopies(): int
    {
        return $this->books->countTotalCopies();
    }

    private function normalizeBookData(array $data): array
    {
        return [
            'Title' => trim((string) ($data['Title'] ?? '')),
            'author' => trim((string) ($data['author'] ?? '')),
            'ISBN' => trim((string) ($data['ISBN'] ?? '')),
            'Genre' => trim((string) ($data['Genre'] ?? '')),
            'published_year' => trim((string) ($data['published_year'] ?? '')),
            'cover_url' => trim((string) ($data['cover_url'] ?? '')),
            'Description' => trim((string) ($data['Description'] ?? '')),
            'total_copies' => max(0, (int) ($data['total_copies'] ?? 0)),
        ];
    }

    private function filterAndSortBooks(array $books, string $search, string $sort, string $direction): array
    {
        $filteredBooks = $books;

        if ($search !== '') {
            $searchTerm = mb_strtolower($search);
            $filteredBooks = [];

            foreach ($books as $book) {
                $title = mb_strtolower((string) ($book['Title'] ?? $book['title'] ?? ''));
                $author = mb_strtolower((string) ($book['author'] ?? $book['Author'] ?? ''));

                if (str_contains($title, $searchTerm) || str_contains($author, $searchTerm)) {
                    $filteredBooks[] = $book;
                }
            }
        }

        $sort = strtolower(trim($sort));

        usort($filteredBooks, function (array $left, array $right) use ($sort, $direction): int {
            $leftValue = $this->getBookValueForSorting($left, $sort);
            $rightValue = $this->getBookValueForSorting($right, $sort);
            $comparison = $leftValue <=> $rightValue;

            if (strtolower(trim($direction)) === 'desc') {
                return -$comparison;
            }

            return $comparison;
        });

        return $filteredBooks;
    }

    private function getBookValueForSorting(array $book, string $sort): string
    {
        if ($sort === 'author') {
            return mb_strtolower((string) ($book['author'] ?? $book['Author'] ?? ''));
        }

        if ($sort === 'published') {
            return str_pad((string) ($book['published_year'] ?? $book['publishedYear'] ?? ''), 10, '0', STR_PAD_LEFT);
        }

        return mb_strtolower((string) ($book['Title'] ?? $book['title'] ?? ''));
    }
}
