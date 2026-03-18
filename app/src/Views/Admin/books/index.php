<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><?= htmlspecialchars($adminBooksViewModel->title) ?></h3>
    <a class="btn btn-primary" href="/admin/books/create">+ Add New Book</a>
  </div>

  <form class="d-flex gap-2 mb-3" method="get" action="/admin/books">
    <input class="form-control" name="q" value="<?= htmlspecialchars($adminBooksViewModel->searchQuery) ?>" placeholder="Search title/author">
    <button class="btn btn-outline-primary">Search</button>
  </form>

  <?php if (empty($adminBooksViewModel->books)): ?>
    <div class="text-muted p-4">No books found.</div>
  <?php else: ?>
    <div id="admin-book-list" class="admin-list admin-books">
      <?php foreach ($adminBooksViewModel->books as $book): ?>
        <div class="list-card card-soft mb-3">
          <div class="list-inner d-flex align-items-center gap-3">
            <div class="list-thumb flex-shrink-0">
              <img class="admin-cover" src="<?= htmlspecialchars($book['coverPath']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            </div>

            <div class="list-details flex-grow-1" style="min-width:0;">
              <div class="list-title fw-semibold"><?= htmlspecialchars($book['title']) ?></div>

              <div class="text-muted small">
                Author: <?= htmlspecialchars($book['author']) ?>
                <?php if ($book['genre'] !== ''): ?> &bull; <?= htmlspecialchars($book['genre']) ?><?php endif; ?>
                <?php if ($book['publishedYear'] !== ''): ?> &bull; <?= htmlspecialchars($book['publishedYear']) ?><?php endif; ?>
              </div>

              <div class="list-meta mt-2 small text-muted d-flex flex-wrap gap-2 align-items-center">
                <?php if ($book['isbn'] !== ''): ?><div># <?= htmlspecialchars($book['isbn']) ?></div><?php endif; ?>
                <?php if ($book['totalCopies'] !== null): ?><div>&bull; Copies: <?= $book['totalCopies'] ?></div><?php endif; ?>
              </div>

              <div class="mt-2">
                <?php if ($book['availableCopies'] !== null): ?>
                  <?php if ($book['availableCopies'] > 0): ?>
                    <span class="badge badge-available"><?= $book['availableCopies'] ?>/<?= $book['totalCopies'] ?? $book['availableCopies'] ?></span>
                  <?php else: ?>
                    <span class="badge badge-unavailable">Unavailable</span>
                  <?php endif; ?>
                <?php endif; ?>
              </div>

              <div class="list-actions-mobile d-flex d-md-none gap-2 mt-3 flex-wrap">
                <a class="btn btn-sm btn-warning" href="/admin/books/edit?id=<?= $book['id'] ?>">Edit</a>
                <a class="btn btn-sm btn-outline-primary" href="/books/<?= $book['id'] ?>">View</a>
                <form method="post" action="/admin/books/delete" class="d-inline" onsubmit="return confirm('Delete this book?');">
                  <input type="hidden" name="id" value="<?= $book['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </div>
            </div>

            <div class="list-actions d-none d-md-flex ms-auto flex-nowrap gap-2 align-items-center">
              <a class="btn btn-sm btn-warning" href="/admin/books/edit?id=<?= $book['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-outline-primary" href="/books/<?= $book['id'] ?>">View</a>
              <form method="post" action="/admin/books/delete" class="d-inline mb-0" onsubmit="return confirm('Delete this book?');">
                <input type="hidden" name="id" value="<?= $book['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
