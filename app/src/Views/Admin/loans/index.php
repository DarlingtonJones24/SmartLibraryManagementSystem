<div class="container py-4">
  <h3 class="mb-3">Manage Loans</h3>

  <div>
    <?php if (empty($loans)): ?>
      <div class="text-muted">No active loans.</div>
    <?php else: ?>
      <div id="admin-loan-list">
        <?php foreach ($loans as $l): ?>
          <?php
            $title = $l['Title'] ?? '';
            $author = $l['author'] ?? '';
            $cover = trim((string)($l['cover_url'] ?? ''));
            if ($cover === '') {
              $coverPath = '/assets/Uploads/covers/default-cover.svg';
            } else {
              $coverPath = preg_match('#(^/|assets/Uploads/)#i', $cover) ? '/' . ltrim($cover, '/') : '/assets/Uploads/covers/' . rawurlencode($cover);
            }
          ?>
          <div class="list-card mb-3">
            <div class="list-inner">
              <div class="list-thumb"><img src="<?= htmlspecialchars($coverPath) ?>" alt="<?= htmlspecialchars($title) ?>"></div>
              <div class="list-details">
                <div class="list-title"><?= htmlspecialchars($title) ?></div>
                <div class="list-meta">Author: <?= htmlspecialchars($author) ?> • Loaned: <?= htmlspecialchars($l['loaned_at'] ?? '') ?> • Due: <?= htmlspecialchars($l['due_at'] ?? '') ?></div>
                <div class="small text-muted">Borrower: <?= htmlspecialchars($l['user_name'] ?? $l['user_id'] ?? '') ?></div>
              </div>
              <div class="list-actions">
                <a class="btn btn-sm btn-secondary" href="/admin/loans/show?id=<?= (int)$l['id'] ?>">View</a>
                <form method="post" action="/index.php?route=loan/return" class="d-inline">
                  <input type="hidden" name="loan_id" value="<?= (int)$l['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-success">Mark Returned</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
