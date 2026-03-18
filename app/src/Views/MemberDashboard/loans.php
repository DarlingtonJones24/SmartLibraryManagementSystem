<div class="container py-4">
  <h1 class="h3 mb-3"><?= htmlspecialchars($memberLoansViewModel->title) ?></h1>
  <div data-js="page-message"></div>

  <?php if (empty($memberLoansViewModel->loans)): ?>
    <div class="card p-4 text-muted">You have no active loans.</div>
  <?php else: ?>
    <div class="list-group" data-js="ajax-list" data-empty-message="You have no active loans.">
      <?php foreach ($memberLoansViewModel->loans as $loan): ?>
      <div class="card mb-3 p-3">
        <div class="d-flex">
          <div style="width:72px;flex:0 0 72px;margin-right:12px">
            <img src="<?= htmlspecialchars($loan['coverPath']) ?>" alt="<?= htmlspecialchars($loan['title']) ?>" class="img-fluid" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
          </div>
          <div class="flex-grow-1">
            <div class="fw-bold"><?= htmlspecialchars($loan['title']) ?></div>
            <div class="small text-muted">Author: <?= htmlspecialchars($loan['author']) ?></div>
            <div class="small text-muted mt-2">
              Loaned: <?= htmlspecialchars($loan['loanedAt']) ?> &bull; Due: <?= htmlspecialchars($loan['dueAt']) ?>
            </div>
            <?php if ($loan['isOverdue']): ?><span class="badge badge-unavailable mt-2">Overdue</span><?php endif; ?>
          </div>
          <div class="text-end">
            <form method="post" action="/loan/return" data-js="ajax-action-form">
              <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
              <button type="submit" class="btn btn-outline-primary">Return</button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
