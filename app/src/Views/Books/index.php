<?php
$suppressCatalogHeader = $suppressCatalogHeader ?? false;
$suppressCatalogContainer = $suppressCatalogContainer ?? false;
?>

<?php if (!$suppressCatalogContainer): ?>
<div class="container py-4">
  <div class="catalog-container">
    <div class="catalog-main">
<?php endif; ?>

<?php if (!$suppressCatalogHeader): ?>
      <div class="page-header">
        <div class="page-title">Book Catalog</div>
        <div class="page-subtitle">Browse and search our entire collection of books.</div>
      </div>
<?php endif; ?>

      <form class="catalog-search" method="get" action="/catalog" role="search" aria-label="Catalog search">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
          <input type="search" name="q" value="<?= htmlspecialchars($q ?? '') ?>" class="form-control" placeholder="Search by Title..." aria-label="Search books by title">
          <button class="btn btn-accent" type="submit">Search</button>
        </div>
      </form>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <?php
          $currentFilter = trim($filter ?? '');
          $currentSort = strtolower(trim($sort ?? ($_GET['sort'] ?? 'title')));
          $currentDirection = strtolower(trim($direction ?? ($_GET['direction'] ?? 'asc')));
          $isLoggedIn = \App\Framework\Auth::check();
          $qs = $_GET;
        ?>
        <div class="filters d-flex gap-2 align-items-center">
          <?php
            $allQs = $qs;
            unset($allQs['filter']);
          ?>
          <a href="?<?= http_build_query($allQs) ?>" class="btn btn-filter<?= $currentFilter === '' ? ' active' : '' ?>">All</a>

          <?php $availQs = array_merge($qs, ['filter' => 'available']); ?>
          <a href="?<?= http_build_query($availQs) ?>" class="btn btn-filter<?= $currentFilter === 'available' ? ' active' : '' ?>">Available</a>

          <?php if ($isLoggedIn):
            $overQs = array_merge($qs, ['filter' => 'overdue']); ?>
            <a href="?<?= http_build_query($overQs) ?>" class="btn btn-filter<?= $currentFilter === 'overdue' ? ' active' : '' ?>">Overdue</a>
          <?php else: ?>
            <a href="/login" class="btn btn-filter">Overdue</a>
          <?php endif; ?>

          <?php if ($isLoggedIn):
            $resQs = array_merge($qs, ['filter' => 'reserved']); ?>
            <a href="?<?= http_build_query($resQs) ?>" class="btn btn-filter<?= $currentFilter === 'reserved' ? ' active' : '' ?>">Reserved</a>
          <?php else: ?>
            <a href="/login" class="btn btn-filter">Reserved</a>
          <?php endif; ?>
        </div>

        <div class="d-flex gap-2 align-items-center">
          <form method="get" action="" class="d-flex gap-2 align-items-center">
            <?php if (($q ?? '') !== ''): ?>
              <input type="hidden" name="q" value="<?= htmlspecialchars((string)$q) ?>">
            <?php endif; ?>
            <?php if ($currentFilter !== ''): ?>
              <input type="hidden" name="filter" value="<?= htmlspecialchars($currentFilter) ?>">
            <?php endif; ?>
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="title" <?= $currentSort === 'title' ? 'selected' : '' ?>>Title</option>
              <option value="author" <?= $currentSort === 'author' ? 'selected' : '' ?>>Author</option>
              <option value="published" <?= $currentSort === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
            <select name="direction" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="asc" <?= $currentDirection === 'asc' ? 'selected' : '' ?>>Ascending</option>
              <option value="desc" <?= $currentDirection === 'desc' ? 'selected' : '' ?>>Descending</option>
            </select>
          </form>
          <div class="btn-group" role="group" aria-label="View toggle">
            <button type="button" class="btn btn-light btn-sm" title="List view" data-view="list"><i class="bi bi-list"></i></button>
            <button type="button" class="btn btn-light btn-sm" title="Grid view" data-view="grid"><i class="bi bi-grid-3x3-gap"></i></button>
          </div>
        </div>
      </div>

      <?php if (empty($books)): ?>
        <div class="alert alert-info">No books found.</div>
      <?php else: ?>
        <?php
          $page = max(1, (int)($_GET['p'] ?? 1));
          $perPage = 4;
          $total = count($books);
          $totalPages = (int)max(1, ceil($total / $perPage));
          if ($page > $totalPages) $page = $totalPages;
          $start = ($page - 1) * $perPage;
          $pageItems = array_slice($books, $start, $perPage);
        ?>

        <div id="catalog-results" class="catalog-results list d-flex flex-column gap-3">
          <?php foreach ($pageItems as $b):
            $file = $b['cover_url'] ?? $b['cover'] ?? $b['coverPath'] ?? '';
            if (trim($file) === '') {
              $coverPath = '/assets/Uploads/covers/default-cover.svg';
            } else {
              if (preg_match('#(^/|assets/Uploads/)#i', $file)) {
                $coverPath = '/' . ltrim($file, '/');
              } else {
                $coverPath = '/assets/Uploads/covers/' . rawurlencode($file);
              }
            }

            $title = $b['title'] ?? $b['Title'] ?? '';
            $author = $b['author'] ?? $b['Author'] ?? '';
            $genre = $b['genre'] ?? $b['Genre'] ?? '';
            $type = $b['type'] ?? $b['Type'] ?? '';
            $isbn = $b['isbn'] ?? $b['ISBN'] ?? '';
            $published = $b['published_year'] ?? $b['publishedYear'] ?? $b['published'] ?? '';
            $available = $b['available'] ?? $b['available_count'] ?? $b['AvailableCopies'] ?? null;
            $totalCopies = $b['total_copies'] ?? $b['totalCopies'] ?? $b['TotalCopies'] ?? null;
          ?>

          <div class="list-card">
            <div class="list-thumb">
              <img src="<?= $coverPath ?>" alt="<?= htmlspecialchars($b['Title'] ?? '') ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
            </div>
            <div class="list-details">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="list-title"><?= htmlspecialchars($title) ?></div>
                  <div class="text-muted small">Author: <?= htmlspecialchars($author) ?></div>
                </div>
                <div class="ms-3 text-end d-none d-md-block">
                  <?php if ($available !== null && (int)$available > 0): ?>
                    <?php if (\App\Framework\Auth::check()): ?>
                      <form method="post" action="/loan/borrow" class="d-inline">
                        <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                        <button type="submit" class="btn btn-accent">Borrow</button>
                      </form>
                    <?php else: ?>
                      <a href="/login" class="btn btn-accent">Borrow</a>
                    <?php endif; ?>
                  <?php elseif ($available !== null && (int)$available <= 0): ?>
                      <?php if (\App\Framework\Auth::check()): ?>
                      <form method="post" action="/reserve" class="d-inline">
                        <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                        <button type="submit" class="btn btn-outline-secondary">Reserve</button>
                      </form>
                    <?php else: ?>
                      <a href="/login" class="btn btn-outline-secondary">Reserve</a>
                    <?php endif; ?>
                  <?php else: ?>
                    <?php if ($totalCopies !== null && (int)$totalCopies === 0): ?>
                      <?php if (\App\Framework\Auth::check()): ?>
                        <form method="post" action="/reserve" class="d-inline">
                          <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                          <button type="submit" class="btn btn-outline-secondary">Reserve</button>
                        </form>
                      <?php else: ?>
                        <a href="/login" class="btn btn-outline-secondary">Reserve</a>
                      <?php endif; ?>
                    <?php else: ?>
                      <?php if (\App\Framework\Auth::check()): ?>
                        <form method="post" action="/loan/borrow" class="d-inline">
                          <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                          <button type="submit" class="btn btn-accent">Borrow</button>
                        </form>
                      <?php else: ?>
                        <a href="/login" class="btn btn-accent">Borrow</a>
                      <?php endif; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>

              <div class="list-meta mt-2 small text-muted d-flex flex-wrap gap-2 align-items-center">
                <?php if (!empty($genre)): ?>
                  <div><i class="bi bi-bookmark me-1" aria-hidden="true"></i><?= htmlspecialchars($genre) ?></div>
                <?php endif; ?>
                <?php if (!empty($type)): ?> <div>• <?= htmlspecialchars($type) ?></div><?php endif; ?>
                <?php if (!empty($isbn)): ?> <div>• # <?= htmlspecialchars($isbn) ?></div><?php endif; ?>
                <?php if (!empty($published)): ?> <div>• <?= htmlspecialchars($published) ?></div><?php endif; ?>
                <?php if ($totalCopies !== null): ?> <div>• Copies: <?= (int)$totalCopies ?></div><?php endif; ?>
              </div>

              <div class="mt-2">
                <?php if ($available !== null): ?>
                  <?php if ((int)$available > 0): ?>
                    <span class="badge badge-available">Available (<?= (int)$available ?>)</span>
                    <?php if ((int)$available <= 2): ?> <span class="badge badge-low">Low Stock</span><?php endif; ?>
                  <?php else: ?>
                    <span class="badge badge-unavailable">Unavailable</span>
                  <?php endif; ?>
                <?php elseif ($totalCopies !== null): ?>
                  <?php if ((int)$totalCopies === 0): ?>
                    <span class="badge badge-unavailable">Unavailable</span>
                  <?php else: ?>
                    <span class="badge badge-info">Total copies: <?= (int)$totalCopies ?></span>
                    <?php if ((int)$totalCopies <= 2): ?> <span class="badge badge-low">Low Stock</span><?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="list-actions d-block d-md-none">
              <?php if ($available !== null && (int)$available > 0): ?>
                <?php if (\App\Framework\Auth::check()): ?>
                  <form method="post" action="/loan/borrow" class="d-inline">
                    <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                    <button type="submit" class="btn btn-accent">Borrow</button>
                  </form>
                <?php else: ?>
                  <a href="/login" class="btn btn-accent">Borrow</a>
                <?php endif; ?>
              <?php elseif ($available !== null && (int)$available <= 0): ?>
                <?php if (\App\Framework\Auth::check()): ?>
                  <form method="post" action="/reserve" class="d-inline">
                    <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                    <button type="submit" class="btn btn-outline-secondary">Reserve</button>
                  </form>
                <?php else: ?>
                  <a href="/login" class="btn btn-outline-secondary">Reserve</a>
                <?php endif; ?>
              <?php else: ?>
                <?php if ($totalCopies !== null && (int)$totalCopies === 0): ?>
                    <?php if (\App\Framework\Auth::check()): ?>
                      <form method="post" action="/reserve" class="d-inline">
                      <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                      <button type="submit" class="btn btn-outline-secondary">Reserve</button>
                    </form>
                  <?php else: ?>
                    <a href="/login" class="btn btn-outline-secondary">Reserve</a>
                  <?php endif; ?>
                <?php else: ?>
                  <?php if (\App\Framework\Auth::check()): ?>
                    <form method="post" action="/loan/borrow" class="d-inline">
                      <input type="hidden" name="book_id" value="<?= (int)$b['id'] ?>">
                      <button type="submit" class="btn btn-accent">Borrow</button>
                    </form>
                  <?php else: ?>
                    <a href="/login" class="btn btn-accent">Borrow</a>
                  <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <?php endforeach; ?>
        </div>

        <!-- pagination -->
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination">
            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['p' => $page - 1])) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item<?= $i === $page ? ' active' : '' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['p' => $page + 1])) ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    </div>

<?php if (!$suppressCatalogContainer): ?>
  </div>
</div>
<?php endif; ?>
                                    
