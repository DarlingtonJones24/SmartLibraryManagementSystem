<div class="admin-dashboard-page">
  <div class="mb-3">
    <div class="admin-page-title">Admin Dashboard</div>
    <div class="admin-page-subtitle">Overview of your library's operations.</div>
  </div>

  <div class="dashboard-stats">
    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Total Books</div>
        <div class="stat-value"><?= $adminDashboardViewModel->totalBooks ?></div>
        <div class="stat-sub"><?= $adminDashboardViewModel->totalCopies ?> total copies</div>
      </div>
      <div class="stat-icon book" aria-hidden><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V5H6.5A2.5 2.5 0 0 0 4 7.5v12z"/></svg></div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Available</div>
        <div class="stat-value"><?= $adminDashboardViewModel->availableCopies ?></div>
        <div class="stat-sub">of <?= $adminDashboardViewModel->totalCopies ?> copies</div>
      </div>
      <div class="stat-icon available" aria-hidden><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1 14l-4-4 1.4-1.4L11 13.2l5.6-5.6L18 9l-7 7z"/></svg></div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Active Loans</div>
        <div class="stat-value"><?= $adminDashboardViewModel->activeLoans ?></div>
        <div class="stat-sub">Currently borrowed</div>
      </div>
      <div class="stat-icon loans" aria-hidden><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10l3-2 4 4V3z"/></svg></div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Overdue</div>
        <div class="stat-value"><?= $adminDashboardViewModel->overdueLoans ?></div>
        <div class="stat-sub">Need attention</div>
      </div>
      <div class="stat-icon overdue" aria-hidden><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 11H11V6h2v7z"/></svg></div>
    </div>

    <div class="stat-card">
      <div class="stat-left">
        <div class="stat-desc">Reservations</div>
        <div class="stat-value"><?= $adminDashboardViewModel->pendingReservations ?></div>
        <div class="stat-sub">Pending pickup</div>
      </div>
      <div class="stat-icon reservations" aria-hidden><svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5H7z"/></svg></div>
    </div>
  </div>

  <div class="dashboard-panels">
    <div class="recent-panel">
      <h5>Recent Loans</h5>
      <?php if (empty($adminDashboardViewModel->recentLoans)): ?>
        <div class="text-muted">No recent loans.</div>
      <?php else: ?>
        <div class="recent-list">
          <?php foreach ($adminDashboardViewModel->recentLoans as $loan): ?>
            <div class="recent-item">
              <div class="recent-thumb">
                <img src="<?= htmlspecialchars($loan['coverPath']) ?>" alt="<?= htmlspecialchars($loan['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
              </div>
              <div class="recent-details">
                <div class="recent-title"><?= htmlspecialchars($loan['title']) ?></div>
                <div class="recent-meta">Due: <?= htmlspecialchars($loan['dueAt'] !== '' ? $loan['dueAt'] : $loan['loanedAt']) ?></div>
              </div>
              <div class="status-badge <?= $loan['isOverdue'] ? 'badge-overdue' : 'badge-active' ?>"><?= $loan['isOverdue'] ? 'overdue' : 'active' ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="recent-panel">
      <h5>Recent Reservations</h5>
      <?php if (empty($adminDashboardViewModel->recentReservations)): ?>
        <div class="text-muted">No recent reservations.</div>
      <?php else: ?>
        <div class="recent-list">
          <?php foreach ($adminDashboardViewModel->recentReservations as $reservation): ?>
            <div class="recent-item">
              <div class="recent-thumb">
                <img src="<?= htmlspecialchars($reservation['coverPath']) ?>" alt="<?= htmlspecialchars($reservation['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
              </div>
              <div class="recent-details">
                <div class="recent-title"><?= htmlspecialchars($reservation['title']) ?></div>
                <div class="recent-meta">Expires: <?= htmlspecialchars($reservation['expiresAt'] !== '' ? $reservation['expiresAt'] : $reservation['createdAt']) ?></div>
              </div>
              <div class="status-badge <?= htmlspecialchars($reservation['statusClass']) ?>"><?= htmlspecialchars($reservation['statusLabel']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
