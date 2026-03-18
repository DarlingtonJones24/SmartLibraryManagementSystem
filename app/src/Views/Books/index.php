<?php
$catalogViewModel = $catalogViewModel ?? null;
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

      <form class="catalog-search" method="get" action="/catalog" role="search" aria-label="Catalog search" data-js="catalog-search-form">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
          <input type="search" name="q" value="<?= htmlspecialchars($catalogViewModel->searchQuery) ?>" class="form-control" placeholder="Search by Title..." aria-label="Search books by title" data-js="catalog-search-input">
          <button class="btn btn-accent" type="submit">Search</button>
        </div>
      </form>
      <div class="form-text mt-2">Live results appear while you type.</div>
      <div class="mt-3" data-js="catalog-live-results"></div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <?php $query = $_GET; ?>
        <div class="filters d-flex gap-2 align-items-center">
          <?php $allQuery = $query; unset($allQuery['filter']); ?>
          <a href="?<?= http_build_query($allQuery) ?>" class="btn btn-filter<?= $catalogViewModel->filter === '' ? ' active' : '' ?>">All</a>

          <?php $availableQuery = array_merge($query, ['filter' => 'available']); ?>
          <a href="?<?= http_build_query($availableQuery) ?>" class="btn btn-filter<?= $catalogViewModel->filter === 'available' ? ' active' : '' ?>">Available</a>

          <?php if ($catalogViewModel->isLoggedIn): ?>
            <?php $overdueQuery = array_merge($query, ['filter' => 'overdue']); ?>
            <a href="?<?= http_build_query($overdueQuery) ?>" class="btn btn-filter<?= $catalogViewModel->filter === 'overdue' ? ' active' : '' ?>">Overdue</a>
            <?php $reservedQuery = array_merge($query, ['filter' => 'reserved']); ?>
            <a href="?<?= http_build_query($reservedQuery) ?>" class="btn btn-filter<?= $catalogViewModel->filter === 'reserved' ? ' active' : '' ?>">Reserved</a>
          <?php else: ?>
            <a href="/login" class="btn btn-filter">Overdue</a>
            <a href="/login" class="btn btn-filter">Reserved</a>
          <?php endif; ?>
        </div>

        <div class="d-flex gap-2 align-items-center">
          <form method="get" action="" class="d-flex gap-2 align-items-center">
            <?php if ($catalogViewModel->searchQuery !== ''): ?>
              <input type="hidden" name="q" value="<?= htmlspecialchars($catalogViewModel->searchQuery) ?>">
            <?php endif; ?>
            <?php if ($catalogViewModel->filter !== ''): ?>
              <input type="hidden" name="filter" value="<?= htmlspecialchars($catalogViewModel->filter) ?>">
            <?php endif; ?>
            <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="title" <?= $catalogViewModel->sort === 'title' ? 'selected' : '' ?>>Title</option>
              <option value="author" <?= $catalogViewModel->sort === 'author' ? 'selected' : '' ?>>Author</option>
              <option value="published" <?= $catalogViewModel->sort === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
            <select name="direction" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="asc" <?= $catalogViewModel->direction === 'asc' ? 'selected' : '' ?>>Ascending</option>
              <option value="desc" <?= $catalogViewModel->direction === 'desc' ? 'selected' : '' ?>>Descending</option>
            </select>
          </form>
          <div class="btn-group" role="group" aria-label="View toggle">
            <button type="button" class="btn btn-light btn-sm" title="List view" data-view="list"><i class="bi bi-list"></i></button>
            <button type="button" class="btn btn-light btn-sm" title="Grid view" data-view="grid"><i class="bi bi-grid-3x3-gap"></i></button>
          </div>
        </div>
      </div>

      <?php if (empty($catalogViewModel->books)): ?>
        <div class="alert alert-info">No books found.</div>
      <?php else: ?>
        <div id="catalog-results" class="catalog-results list d-flex flex-column gap-3">
          <?php foreach ($catalogViewModel->pageItems as $book): ?>
          <div class="list-card">
            <div class="list-thumb">
              <img src="<?= htmlspecialchars($book['coverPath']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
            </div>
            <div class="list-details">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="list-title"><?= htmlspecialchars($book['title']) ?></div>
                  <div class="text-muted small">Author: <?= htmlspecialchars($book['author']) ?></div>
                </div>
                <div class="ms-3 text-end d-none d-md-block">
                  <?php if ($book['canBorrow']): ?>
                    <?php if ($catalogViewModel->isLoggedIn): ?>
                      <form method="post" action="/loan/borrow" class="d-inline">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <button type="submit" class="btn btn-accent">Borrow</button>
                      </form>
                    <?php else: ?>
                      <a href="/login" class="btn btn-accent">Borrow</a>
                    <?php endif; ?>
                  <?php else: ?>
                    <?php if ($catalogViewModel->isLoggedIn): ?>
                      <form method="post" action="/reserve" class="d-inline">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <button type="submit" class="btn btn-outline-secondary">Reserve</button>
                      </form>
                    <?php else: ?>
                      <a href="/login" class="btn btn-outline-secondary">Reserve</a>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>

              <div class="list-meta mt-2 small text-muted d-flex flex-wrap gap-2 align-items-center">
                <?php if ($book['genre'] !== ''): ?><div><i class="bi bi-bookmark me-1" aria-hidden="true"></i><?= htmlspecialchars($book['genre']) ?></div><?php endif; ?>
                <?php if ($book['type'] !== ''): ?><div>&bull; <?= htmlspecialchars($book['type']) ?></div><?php endif; ?>
                <?php if ($book['isbn'] !== ''): ?><div>&bull; # <?= htmlspecialchars($book['isbn']) ?></div><?php endif; ?>
                <?php if ($book['publishedYear'] !== ''): ?><div>&bull; <?= htmlspecialchars($book['publishedYear']) ?></div><?php endif; ?>
                <?php if ($book['totalCopies'] !== null): ?><div>&bull; Copies: <?= $book['totalCopies'] ?></div><?php endif; ?>
              </div>

              <div class="mt-2">
                <?php if ($book['availabilityText'] !== ''): ?>
                  <span class="badge <?= htmlspecialchars($book['availabilityClass']) ?>"><?= htmlspecialchars($book['availabilityText']) ?></span>
                <?php endif; ?>
                <?php if ($book['showLowStock']): ?><span class="badge badge-low">Low Stock</span><?php endif; ?>
              </div>
            </div>

            <div class="list-actions d-block d-md-none">
              <?php if ($book['canBorrow']): ?>
                <?php if ($catalogViewModel->isLoggedIn): ?>
                  <form method="post" action="/loan/borrow" class="d-inline">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-accent">Borrow</button>
                  </form>
                <?php else: ?>
                  <a href="/login" class="btn btn-accent">Borrow</a>
                <?php endif; ?>
              <?php else: ?>
                <?php if ($catalogViewModel->isLoggedIn): ?>
                  <form method="post" action="/reserve" class="d-inline">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-outline-secondary">Reserve</button>
                  </form>
                <?php else: ?>
                  <a href="/login" class="btn btn-outline-secondary">Reserve</a>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination">
            <li class="page-item<?= $catalogViewModel->currentPage <= 1 ? ' disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['p' => $catalogViewModel->currentPage - 1])) ?>">Previous</a>
            </li>
            <?php for ($page = 1; $page <= $catalogViewModel->totalPages; $page++): ?>
              <li class="page-item<?= $page === $catalogViewModel->currentPage ? ' active' : '' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['p' => $page])) ?>"><?= $page ?></a></li>
            <?php endfor; ?>
            <li class="page-item<?= $catalogViewModel->currentPage >= $catalogViewModel->totalPages ? ' disabled' : '' ?>">
              <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['p' => $catalogViewModel->currentPage + 1])) ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    </div>

<?php if (!$suppressCatalogContainer): ?>
  </div>
</div>
<?php endif; ?>
