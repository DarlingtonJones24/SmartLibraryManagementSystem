<?php

namespace App\Models;

class Loan
{
    public int $id;
    public int $user_id;
    public int $copy_id;
    public ?string $loaned_at;
    public ?string $due_at;
    public ?string $returned_at;
    public int $renew_count;
    public float $fine_amount;
    public string $user_name;
    public string $book_title;
    public string $cover_url;
    public bool $is_overdue;
    public string $status_label;

    public static function fromArray(array $row): self
    {
        $loan = new self();

        $loan->id = (int)($row['id'] ?? 0);
        $loan->user_id = (int)($row['user_id'] ?? 0);
        $loan->copy_id = (int)($row['copy_id'] ?? 0);

        $loan->loaned_at = isset($row['loaned_at']) ? (string)$row['loaned_at'] : null;
        $loan->due_at = isset($row['due_at']) ? (string)$row['due_at'] : null;
        $loan->returned_at = isset($row['returned_at']) ? (string)$row['returned_at'] : null;

        $loan->renew_count = (int)($row['renew_count'] ?? 0);
        $loan->fine_amount = (float)($row['fine_amount'] ?? 0);

        $loan->user_name = (string)($row['user_name'] ?? $row['name'] ?? '');
        $loan->book_title = (string)($row['Title'] ?? $row['book_title'] ?? '');
        $loan->cover_url = (string)($row['cover_url'] ?? '');

        $loan->is_overdue = $loan->isOverdue();
        if ($loan->returned_at !== null && $loan->returned_at !== '') {
            $loan->status_label = 'returned';
        } elseif ($loan->is_overdue) {
            $loan->status_label = 'overdue';
        } else {
            $loan->status_label = 'active';
        }

        return $loan;
    }

    public function isOverdue(): bool
    {
        if ($this->returned_at !== null && $this->returned_at !== '') {
            return false;
        }

        if ($this->due_at === null || $this->due_at === '') {
            return false;
        }

        $dueTimestamp = strtotime($this->due_at);
        if ($dueTimestamp === false) {
            return false;
        }

        return $dueTimestamp < time();
    }
}
