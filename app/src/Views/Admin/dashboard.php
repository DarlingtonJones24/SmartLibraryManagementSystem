<?php
use App\Framework\Auth;
?>

<div class="admin-dashboard-page">

  <div class="mb-3">
    <div class="admin-page-title">Admin Dashboard</div>
    <div class="admin-page-subtitle">Overview of your library's operations.</div>
  </div>

  

  

  <!-- STATS -->
  <div class="dashboard-stats">
    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Total Books</div>
        <div class="stat-value"><?= (int)($totalBooks ?? 0) ?></div>
        <div class="stat-sub"><?= (int)($totalCopies ?? 0) ?> total copies</div>
      </div>
      <div class="stat-icon book" aria-hidden>
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V5H6.5A2.5 2.5 0 0 0 4 7.5v12z"/></svg>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
          <div class="stat-desc">Available</div>
          <div class="stat-value"><?= (int)($availableCopies ?? $available ?? 0) ?></div>
          <div class="stat-sub">of <?= (int)($totalCopies ?? 0) ?> copies</div>
        </div>
      <div class="stat-icon available" aria-hidden>
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1 14l-4-4 1.4-1.4L11 13.2l5.6-5.6L18 9l-7 7z"/></svg>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Active Loans</div>
        <div class="stat-value"><?= (int)($activeLoans ?? 0) ?></div>
        <div class="stat-sub">Currently borrowed</div>
      </div>
      <div class="stat-icon loans" aria-hidden>
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10l3-2 4 4V3z"/></svg>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Overdue</div>
        <div class="stat-value"><?= (int)($overdue ?? 0) ?></div>
        <div class="stat-sub">Need attention</div>
      </div>
      <div class="stat-icon overdue" aria-hidden>
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 11H11V6h2v7z"/></svg>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Reservations</div>
        <div class="stat-value"><?= (int)($reservations ?? 0) ?></div>
        <div class="stat-sub">Pending pickup</div>
      </div>
      <div class="stat-icon reservations" aria-hidden>
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z"/></svg>
      </div>
    </div>
  </div>

  <!-- PANELS -->
  <div class="dashboard-panels">
    <div class="recent-panel">
      <h5>Recent Loans</h5>

      <?php if (empty($recentLoans)): ?>
        <div class="text-muted">No recent loans.</div>
      <?php else: ?>
        <div class="recent-list">
          <?php foreach ($recentLoans as $l): ?>
            <?php
              $title = $l['Title'] ?? '';
              $cover = trim((string)($l['cover_url'] ?? $l['cover'] ?? ''));
              $coverPath = $cover === ''
                ? '/assets/Uploads/covers/default-cover.svg'
                : (preg_match('#(^/|assets/Uploads/)#i', $cover) ? '/' . ltrim($cover, '/') : '/assets/Uploads/covers/' . rawurlencode($cover));

              $due = $l['due_at'] ?? '';
              $isOverdue = $due && strtotime($due) < time();
            ?>

            <div class="recent-item">
              <div class="recent-thumb">
                <img src="<?= htmlspecialchars($coverPath) ?>"
                     alt="<?= htmlspecialchars($title) ?>"
                     onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
              </div>

              <div class="recent-details">
                <div class="recent-title"><?= htmlspecialchars($title) ?></div>
                <div class="recent-meta">Due: <?= htmlspecialchars($due ?: ($l['loaned_at'] ?? '')) ?></div>
              </div>

              <div class="status-badge <?= $isOverdue ? 'badge-overdue' : 'badge-active' ?>">
                <?= $isOverdue ? 'overdue' : 'active' ?>
              </div>
            </div>

          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="recent-panel">
      <h5>Recent Reservations</h5>

      <?php if (empty($recentReservations)): ?>
        <div class="text-muted">No recent reservations.</div>
      <?php else: ?>
        <div class="recent-list">
          <?php foreach ($recentReservations as $r): ?>
            <?php
              $title = $r['Title'] ?? '';
              $cover = trim((string)($r['cover_url'] ?? $r['cover'] ?? ''));
              $coverPath = $cover === ''
                ? '/assets/Uploads/covers/default-cover.svg'
                : (preg_match('#(^/|assets/Uploads/)#i', $cover) ? '/' . ltrim($cover, '/') : '/assets/Uploads/covers/' . rawurlencode($cover));

              $expires = $r['expires_at'] ?? $r['expires'] ?? '';
              $expired = $expires && strtotime($expires) < time();
            ?>

            <div class="recent-item">
              <div class="recent-thumb">
                <img src="<?= htmlspecialchars($coverPath) ?>"
                     alt="<?= htmlspecialchars($title) ?>"
                     onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
              </div>

              <div class="recent-details">
                <div class="recent-title"><?= htmlspecialchars($title) ?></div>
                <div class="recent-meta">Expires: <?= htmlspecialchars($expires ?: ($r['created_at'] ?? '')) ?></div>
              </div>

              <?php
                $rawStatus = strtolower(trim($r['Status'] ?? $r['status'] ?? ''));
                $expires = $r['expires_at'] ?? $r['expires'] ?? '';
                $expired = $expires && strtotime($expires) < time();

                if ($rawStatus === 'canceled' || $rawStatus === 'cancelled') {
                    $badgeClass = 'badge-cancelled';
                    $label = 'canceled';
                } elseif ($expired) {
                    $badgeClass = 'badge-overdue';
                    $label = 'expired';
                } elseif ($rawStatus === 'waiting' || $rawStatus === '') {
                    $badgeClass = 'badge-pending';
                    $label = 'pending';
                } else {
                    $badgeClass = 'badge-pending';
                    $label = htmlspecialchars($rawStatus);
                }
              ?>

              <div class="status-badge <?= $badgeClass ?>">
                <?= htmlspecialchars($label) ?>
              </div>
            </div>

          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>
