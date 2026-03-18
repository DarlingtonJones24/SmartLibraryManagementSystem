<div class="container py-4">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h1 class="display-6 mb-1"><?= htmlspecialchars($memberReservationsViewModel->title) ?></h1>
      <div class="text-muted">View and manage your book reservations.</div>
    </div>
  </div>
  <div data-js="page-message"></div>

  <?php if (empty($memberReservationsViewModel->reservations)): ?>
    <div class="card p-4 text-muted">You have no reservations.</div>
  <?php else: ?>
    <div class="list-group" data-js="ajax-list" data-empty-message="You have no reservations.">
      <?php foreach ($memberReservationsViewModel->reservations as $reservation): ?>
      <div class="card mb-3 p-3">
        <div class="d-flex align-items-center">
          <div style="width:72px;flex:0 0 72px;margin-right:16px">
            <img src="<?= htmlspecialchars($reservation['coverPath']) ?>" alt="<?= htmlspecialchars($reservation['title']) ?>" class="img-fluid rounded" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
          </div>
          <div class="flex-grow-1">
            <div class="fw-bold fs-5"><?= htmlspecialchars($reservation['title']) ?></div>
            <div class="small text-muted"><?= htmlspecialchars($reservation['author']) ?></div>
            <div class="small text-muted mt-2">
              Reserved: <?= htmlspecialchars($reservation['createdAt']) ?>
              <?php if ($reservation['expiresAt'] !== ''): ?> &bull; Expires: <?= htmlspecialchars($reservation['expiresAt']) ?><?php endif; ?>
            </div>
          </div>
          <div class="text-end">
            <span class="badge <?= htmlspecialchars($reservation['statusClass']) ?> rounded-pill text-uppercase" style="padding:.45rem .6rem;margin-right:.6rem;"><?= htmlspecialchars($reservation['statusLabel']) ?></span>
            <?php if ($reservation['canPickup']): ?>
              <a href="/books/<?= $reservation['bookId'] ?>" class="btn btn-accent">Pickup</a>
            <?php else: ?>
              <form method="post" action="/reserve/cancel" class="d-inline" data-js="ajax-action-form">
                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                <button type="submit" class="btn btn-outline-danger">Cancel</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
