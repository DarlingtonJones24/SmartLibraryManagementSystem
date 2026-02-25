<div class="container py-4">
  <h3 class="mb-3">Manage Reservations</h3>

  <div class="card card-soft p-3">
    <?php if (empty($reservations)): ?>
      <div class="text-muted">No reservations.</div>
    <?php else: ?>
      <div id="admin-reservation-list">
        <?php foreach ($reservations as $r): ?>
          <?php
            $title = $r['Title'] ?? '';
            $author = $r['author'] ?? '';
            $cover = trim((string)($r['cover_url'] ?? ''));
            if ($cover === '') {
              $coverPath = '/assets/Uploads/covers/default-cover.svg';
            } else {
              $coverPath = preg_match('#(^/|assets/Uploads/)#i', $cover) ? '/' . ltrim($cover, '/') : '/assets/Uploads/covers/' . rawurlencode($cover);
            }
            $reservedOn = $r['created_at'] ?? $r['reserved_at'] ?? '';
            $userName = $r['user_name'] ?? $r['user_id'] ?? '';
          ?>

          <div class="list-card mb-3">
            <div class="list-inner d-flex align-items-center">
              <div class="list-thumb flex-shrink-0 me-3"><img class="admin-cover" src="<?= htmlspecialchars($coverPath) ?>" alt="<?= htmlspecialchars($title) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'"></div>

              <div class="list-details flex-grow-1" style="min-width:0">
                <div class="list-title fw-semibold"><?= htmlspecialchars($title) ?></div>
                <div class="list-meta small text-muted">Author: <?= htmlspecialchars($author) ?> • Reserved On: <?= htmlspecialchars($reservedOn) ?></div>
                <div class="small text-muted">User: <?= htmlspecialchars($userName) ?></div>
              </div>

              <div class="list-actions ms-auto d-flex gap-2 align-items-center">
                <?php $bookId = (int)($r['book_id'] ?? $r['bookId'] ?? $r['id'] ?? 0); ?>
                <a class="btn btn-sm btn-secondary me-2" href="/books/<?= $bookId ?>">View</a>
                <form method="post" action="/index.php?route=admin/reservation/process" class="m-0">
                  <input type="hidden" name="reservation_id" value="<?= (int)($r['id'] ?? $r['reservation_id'] ?? 0) ?>">
                  <button type="submit" class="btn btn-sm btn-outline-success">Process</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
