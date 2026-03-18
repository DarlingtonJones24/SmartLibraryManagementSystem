<div class="container py-4">
  <h3 class="mb-3"><?= htmlspecialchars($adminReservationsViewModel->title) ?></h3>

  <div class="card card-soft p-3">
    <?php if (empty($adminReservationsViewModel->reservations)): ?>
      <div class="text-muted">No reservations.</div>
    <?php else: ?>
      <div id="admin-reservation-list" class="admin-list admin-reservations">
        <?php foreach ($adminReservationsViewModel->reservations as $reservation): ?>
          <div class="list-card mb-3">
            <div class="list-inner d-flex align-items-center">
              <div class="list-thumb flex-shrink-0 me-3">
                <img class="admin-cover" src="<?= htmlspecialchars($reservation['coverPath']) ?>" alt="<?= htmlspecialchars($reservation['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
              </div>

              <div class="list-details flex-grow-1" style="min-width:0">
                <div class="list-title fw-semibold"><?= htmlspecialchars($reservation['title']) ?></div>
                <div class="list-meta small text-muted">Author: <?= htmlspecialchars($reservation['author']) ?> &bull; Reserved On: <?= htmlspecialchars($reservation['createdAt']) ?></div>
                <div class="small text-muted">User: <?= htmlspecialchars($reservation['userName']) ?></div>
              </div>

              <div class="list-actions ms-auto d-flex gap-2 align-items-center">
                <span class="badge <?= htmlspecialchars($reservation['statusClass']) ?>"><?= htmlspecialchars($reservation['statusLabel']) ?></span>
                <a class="btn btn-sm btn-secondary me-2" href="/books/<?= $reservation['bookId'] ?>">View</a>
                <?php if ($reservation['canProcess']): ?>
                  <form method="post" action="/admin/reservation/process" class="m-0">
                    <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-success">Process</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
