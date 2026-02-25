<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Manage Books</h3>
    <a class="btn btn-primary" href="/index.php?route=admin/books/create">+ Add New Book</a>
  </div>

  <form class="d-flex gap-2 mb-3" method="get" action="/index.php">
    <input type="hidden" name="route" value="admin/books">
    <input class="form-control" name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search title/author">
    <button class="btn btn-outline-primary">Search</button>
  </form>

  <?php if (empty($books)): ?>
    <div class="text-muted p-4">No books found.</div>
  <?php else: ?>
    <div id="admin-book-list" class="admin-books">
      <?php foreach (($books ?? []) as $b): ?>
        <?php
          $title  = $b['Title'] ?? '';
          $author = $b['author'] ?? ($b['Author'] ?? '');
          $genre  = $b['Genre'] ?? '';
          $year   = $b['published_year'] ?? '';
          $isbn   = $b['ISBN'] ?? ($b['isbn'] ?? '');
          $cover  = trim((string)($b['cover_url'] ?? ($b['cover'] ?? '')));

          if ($cover === '') {
            $coverPath = '/assets/Uploads/covers/default-cover.svg';
          } else {
            $coverPath = preg_match('#(^/|assets/Uploads/)#i', $cover)
              ? '/' . ltrim($cover, '/')
              : '/assets/Uploads/covers/' . rawurlencode($cover);
          }

          $available = $b['available'] ?? $b['available_copies'] ?? $b['available_count'] ?? null;
          $total     = $b['total'] ?? $b['quantity'] ?? $b['copies'] ?? $b['total_copies'] ?? null;
        ?>

        <div class="list-card card-soft mb-3">
          <div class="list-inner d-flex align-items-center gap-3">
            <!-- LEFT: cover (fixed) -->
            <div class="list-thumb flex-shrink-0">
              <img class="admin-cover" src="<?= htmlspecialchars($coverPath) ?>" alt="<?= htmlspecialchars($title) ?>">
            </div>

            <!-- MIDDLE: details (flex-grow) -->
            <div class="list-details flex-grow-1" style="min-width:0;">
              <div class="list-title fw-semibold"><?= htmlspecialchars($title) ?></div>

              <div class="text-muted small">
                Author: <?= htmlspecialchars($author) ?>
                <?php if ($genre): ?> • <?= htmlspecialchars($genre) ?><?php endif; ?>
                <?php if ($year): ?> • <?= htmlspecialchars($year) ?><?php endif; ?>
              </div>

              <div class="list-meta mt-2 small text-muted d-flex flex-wrap gap-2 align-items-center">
                <?php if (!empty($isbn)): ?><div># <?= htmlspecialchars($isbn) ?></div><?php endif; ?>
                <?php if ($total !== null): ?><div>• Copies: <?= htmlspecialchars($total) ?></div><?php endif; ?>
              </div>

              <div class="mt-2">
                <?php if ($available !== null): ?>
                  <?php if ((int)$available > 0): ?>
                    <span class="badge badge-available"><?= htmlspecialchars($available) ?>/<?= htmlspecialchars($total ?? $available) ?></span>
                  <?php else: ?>
                    <span class="badge badge-unavailable">Unavailable</span>
                  <?php endif; ?>
                <?php endif; ?>
              </div>

              <!-- MOBILE actions (below content) -->
              <div class="list-actions-mobile d-flex d-md-none gap-2 mt-3 flex-wrap">
                <a class="btn btn-sm btn-warning" href="/index.php?route=admin/books/edit&id=<?= (int)$b['id'] ?>">Edit</a>
                <a class="btn btn-sm btn-outline-primary" href="/index.php?route=book/detail&id=<?= (int)$b['id'] ?>">View</a>
                <form method="post" action="/index.php?route=admin/books/delete" class="d-inline" onsubmit="return confirm('Delete this book?');">
                  <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </div>
            </div>

            <!-- RIGHT: actions pinned (desktop) -->
            <div class="list-actions d-none d-md-flex ms-auto flex-nowrap gap-2 align-items-center">
              <a class="btn btn-sm btn-warning" href="/index.php?route=admin/books/edit&id=<?= (int)$b['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-outline-primary" href="/index.php?route=book/detail&id=<?= (int)$b['id'] ?>">View</a>
              <form method="post" action="/index.php?route=admin/books/delete" class="d-inline mb-0" onsubmit="return confirm('Delete this book?');">
                <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
