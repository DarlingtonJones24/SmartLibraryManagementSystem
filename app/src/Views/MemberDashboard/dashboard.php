<div class="container py-4">
  <div class="page-header mb-3">
    <h1 class="display-5">Welcome, <?= htmlspecialchars($memberDashboardViewModel->displayName) ?>!</h1>
    <p class="lead text-muted">Browse our collection and find your next great read.</p>
  </div>

  <div class="mb-3">
    <a class="btn btn-outline-primary me-2" href="/dashboard/loans">
      My loans
      <span class="badge bg-primary ms-1" data-js="loan-count">0</span>
    </a>
    <a class="btn btn-outline-secondary" href="/dashboard/reservation">
      My reservations
      <span class="badge bg-secondary ms-1" data-js="reservation-count">0</span>
    </a>
  </div>
</div>

<?php
$catalogViewModel = $memberDashboardViewModel->catalog;
$suppressCatalogHeader = true;
$suppressCatalogContainer = true;
include __DIR__ . '/../Books/index.php';
?>
