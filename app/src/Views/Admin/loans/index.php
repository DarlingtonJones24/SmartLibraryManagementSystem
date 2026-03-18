<div class="container py-4">
  <h3 class="mb-3"><?= htmlspecialchars($adminLoansViewModel->title) ?></h3>

  <?php if (empty($adminLoansViewModel->loans)): ?>
    <div class="text-muted">No active loans.</div>
  <?php else: ?>
    <div id="admin-loan-list" class="admin-list admin-loans">
      <?php foreach ($adminLoansViewModel->loans as $loan): ?>
        <div class="list-card card-soft mb-3">
          <div class="list-inner d-flex align-items-center gap-3">
            <div class="list-thumb flex-shrink-0">
              <img class="admin-cover" src="<?= htmlspecialchars($loan['coverPath']) ?>" alt="<?= htmlspecialchars($loan['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
            </div>

            <div class="list-details flex-grow-1" style="min-width:0;">
              <div class="list-title fw-semibold"><?= htmlspecialchars($loan['title']) ?></div>
              <div class="list-meta small text-muted">
                Author: <?= htmlspecialchars($loan['author']) ?>
                &bull; Loaned: <?= htmlspecialchars($loan['loanedAt']) ?>
                &bull; Due: <?= htmlspecialchars($loan['dueAt']) ?>
              </div>
              <div class="small text-muted">Borrower: <?= htmlspecialchars($loan['userName']) ?></div>

              <?php if ($loan['isOverdue']): ?>
                <div class="mt-2">
                  <span class="badge badge-unavailable">Overdue</span>
                </div>
              <?php endif; ?>
            </div>

            <div class="list-actions ms-auto d-flex gap-2 align-items-center flex-nowrap">
              <a class="btn btn-sm btn-secondary" href="/admin/loans/show?id=<?= $loan['id'] ?>">View</a>
              <form method="post" action="/admin/loan/return" class="d-inline mb-0">
                <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-success">Mark Returned</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
