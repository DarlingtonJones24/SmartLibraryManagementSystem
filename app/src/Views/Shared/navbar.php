<?php
use App\Framework\Auth;
?>
<div class="app-shell d-flex">
  <?php if (empty($hideSidebar)): ?>
  <aside class="site-sidebar">
    <div class="sidebar-brand px-3 mb-4">
      <a class="d-flex align-items-center gap-2" href="/">
        <div style="width:40px;height:40px;background:var(--accent);border-radius:6px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">SL</div>
        <div>
          <div style="font-weight:700;color:#fff;">Smart <span style="color:var(--accent)">Library</span></div>
        </div>
      </a>
    </div>

    <?php
      $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
      $path = rtrim($path, '/') ?: '/';
      $loggedIn = Auth::check();

      if ($path === '/') {
        $dashboardActive = $loggedIn ? ' active' : '';
        $catalogActive = $loggedIn ? '' : ' active';
      } else {
        $dashboardActive = ($loggedIn && strpos($path, '/dashboard') === 0) ? ' active' : '';
        $catalogActive   = (strpos($path, '/catalog') === 0 || strpos($path, '/books') === 0) ? ' active' : '';
      }
      $loansActive     = (strpos($path, '/loans') === 0) ? ' active' : '';
      $reservActive    = (strpos($path, '/reservations') === 0) ? ' active' : '';
      $settingsActive  = (strpos($path, '/settings') === 0) ? ' active' : '';
    ?>

    <?php
      $user = Auth::check() ? Auth::user() : null;
      $isLibrarian = $user && in_array(strtolower((string)($user['role'] ?? '')), ['librarian','admin']);

      // Active states for admin links
      $adminDashboardActive = (strpos($path, '/admin/dashboard') === 0) ? ' active' : '';
      $adminBooksActive = (strpos($path, '/admin/books') === 0) ? ' active' : '';
      $adminLoansActive = (strpos($path, '/admin/loans') === 0) ? ' active' : '';
      $adminResActive = (strpos($path, '/admin/reservation') === 0) ? ' active' : '';
      $adminSettingsActive = (strpos($path, '/admin/settings') === 0) ? ' active' : '';
    ?>

    <?php if ($isLibrarian): ?>
      <nav class="nav flex-column px-2">
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $adminDashboardActive ?>" href="/admin/dashboard"><i class="bi bi-grid-1x2-fill me-2"></i> Admin Dashboard</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $adminBooksActive ?>" href="/admin/books"><i class="bi bi-book me-2"></i> Manage Books</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $adminLoansActive ?>" href="/admin/loans"><i class="bi bi-journal-bookmark me-2"></i> Manage Loans</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $adminResActive ?>" href="/admin/reservation"><i class="bi bi-calendar-check me-2"></i> Manage Reservations</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $adminSettingsActive ?>" href="/admin/settings"><i class="bi bi-gear me-2"></i> Admin Settings</a>
      </nav>
    <?php else: ?>
      <nav class="nav flex-column px-2">
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $dashboardActive ?>" href="/dashboard"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $catalogActive ?>" href="/catalog"><i class="bi bi-book-half me-2"></i> Catalog</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $loansActive ?>" href="/loans"><i class="bi bi-journal-bookmark me-2"></i> My Loans</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $reservActive ?>" href="/reservations"><i class="bi bi-calendar-check me-2"></i> My Reservations</a>
        <a class="nav-link d-flex align-items-center px-3 py-2<?= $settingsActive ?>" href="/settings"><i class="bi bi-gear me-2"></i> Settings</a>
      </nav>
    <?php endif; ?>

    <div class="sidebar-footer px-3 mt-auto text-white-50">© 2024 Smart Library</div>
  </aside>
  <?php endif; ?>

  <div class="app-main flex-grow-1<?= !empty($hideSidebar) ? ' no-sidebar' : '' ?>">
    <?php if (empty($hideSidebar)): ?>
    <nav class="topbar">
      <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/">Home</a></li>
              <?php $isCatalogActive = trim($catalogActive ?? '') !== ''; ?>
              <li class="breadcrumb-item<?= $isCatalogActive ? ' active' : '' ?>" <?= $isCatalogActive ? 'aria-current="page"' : '' ?>>
                <?php if ($isCatalogActive): ?>
                  Catalog
                <?php else: ?>
                  <a href="/catalog">Catalog</a>
                <?php endif; ?>
              </li>
            </ol>
          </nav>
        </div>

        <div class="d-flex align-items-center gap-3 user-area">
          <?php if (Auth::check()): $u = Auth::user();
                // If name is missing in session, load it once from DB and save it.
                if (empty($u['name']) && !empty($u['id'])) {
                  try {
                    $repo = new \App\Repository\UserRepository();
                    $dbu = $repo->findById((int)$u['id']);
                    if ($dbu && !empty($dbu['name'])) {
                      $u['name'] = $dbu['name'];
                      \App\Framework\Auth::login($u);
                    }
                  } catch (\Throwable $ex) { /* skip name lookup errors here */ }
                }

                $notifCount = (int)($_SESSION['notifications_count'] ?? $_SESSION['notif_count'] ?? 0);
                $displayName = $u['name'] ?? $u['email'] ?? 'User';
                $parts = preg_split('/\s+/', trim($displayName));
                $initials = strtoupper(substr($parts[0] ?? '',0,1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));
          ?>
            <div class="user-welcome text-muted">Welcome, <strong><?= htmlspecialchars($displayName) ?></strong></div>

            <div class="user-notif position-relative">
              <a href="/notifications" class="btn btn-light btn-sm rounded-circle" title="Notifications">
                <i class="bi bi-bell" aria-hidden="true"></i>
              </a>
              <?php if ($notifCount > 0): ?>
                <span class="notif-badge"><?= $notifCount ?></span>
              <?php endif; ?>
            </div>

            <div class="dropdown">
              <a class="btn user-initials" href="#" role="button" data-bs-toggle="dropdown"><?= htmlspecialchars($initials ?: strtoupper(substr($displayName,0,1))) ?></a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li class="px-3 py-2"><strong><?= htmlspecialchars($displayName) ?></strong></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/dashboard">Dashboard</a></li>
                <li><a class="dropdown-item" href="/logout">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a class="btn btn-outline-primary btn-sm" href="/login">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <div class="row">
        <div class="col-12">
          <!-- content -->
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="container-fluid mt-3">
      <div class="row">
        <div class="col-12">
        </div>
      </div>
    </div>
    <?php endif; ?>

<?php
?>
