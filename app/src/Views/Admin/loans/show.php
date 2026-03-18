<?php $loan = $adminLoanDetailViewModel->loan; ?>

<div class="container py-4">
  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h3 class="mb-0">Loan Details</h3>
    <a href="/admin/loans" class="btn btn-sm btn-outline-secondary">Back to loans</a>
  </div>

  <div class="card card-soft p-3 p-md-4">
    <div class="row g-4">
      <div class="col-md-4 col-lg-3">
        <img class="img-fluid rounded" src="<?= htmlspecialchars($loan['coverPath']) ?>" alt="<?= htmlspecialchars($loan['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
      </div>

      <div class="col-md-8 col-lg-9">
        <h2 class="h4 mb-2"><?= htmlspecialchars($loan['title']) ?></h2>
        <div class="text-muted mb-3">Author: <?= htmlspecialchars($loan['author']) ?></div>

        <div class="small text-muted mb-3">
          <?php if ($loan['genre'] !== ''): ?>Genre: <?= htmlspecialchars($loan['genre']) ?> &bull; <?php endif; ?>
          <?php if ($loan['isbn'] !== ''): ?>ISBN: <?= htmlspecialchars($loan['isbn']) ?> &bull; <?php endif; ?>
          <?php if ($loan['publishedYear'] !== ''): ?>Published: <?= htmlspecialchars($loan['publishedYear']) ?><?php endif; ?>
        </div>

        <div class="mb-2"><strong>Borrower:</strong> <?= htmlspecialchars($loan['userName']) ?></div>
        <?php if ($loan['userEmail'] !== ''): ?><div class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($loan['userEmail']) ?></div><?php endif; ?>
        <div class="mb-2"><strong>Loaned At:</strong> <?= htmlspecialchars($loan['loanedAt']) ?></div>
        <div class="mb-2"><strong>Due At:</strong> <?= htmlspecialchars($loan['dueAt']) ?></div>
        <?php if ($loan['returnedAt'] !== ''): ?><div class="mb-3"><strong>Returned At:</strong> <?= htmlspecialchars($loan['returnedAt']) ?></div><?php endif; ?>

        <p class="mb-0"><?= nl2br(htmlspecialchars($loan['description'])) ?></p>
      </div>
    </div>
  </div>
</div>
