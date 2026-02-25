<?php
use App\Framework\Auth;
\App\Framework\TempData::start();
$user = Auth::user();
// If the name is missing in session, I load it from DB once.
if (!empty($user['id']) && empty($user['name'])) {
	try {
		$repo = new \App\Repository\UserRepository();
		$dbu = $repo->findById((int)$user['id']);
		if ($dbu && !empty($dbu['name'])) {
			$user['name'] = $dbu['name'];
			\App\Framework\Auth::login($user);
		}
	} catch (\Throwable $ex) {
		// I ignore lookup errors here.
	}
}

// Display name: use full name first, then email local-part.
$displayName = $user['name'] ?? null;
if (empty($displayName) && !empty($user['email'])) {
	$displayName = strtok($user['email'], '@');
}
?>

<div class="container py-4">
	<div class="page-header mb-3">
		<h1 class="display-5">Welcome, <?= htmlspecialchars($displayName ?? ($user['email'] ?? 'Member')) ?>!</h1>
		<p class="lead text-muted">Browse our collection and find your next great read.</p>
	</div>

	<div class="mb-3">
		<a class="btn btn-outline-primary me-2" href="/dashboard/loans">My loans</a>
		<a class="btn btn-outline-secondary" href="/dashboard/reservation">My reservations</a>
	</div>
</div>

<?php
// For embedded catalog, hide its own header and container.
$suppressCatalogHeader = true;
$suppressCatalogContainer = true;

// Render the catalog below the welcome card.
try {
	$catalogPath = __DIR__ . '/../Books/index.php';
	if (file_exists($catalogPath)) {
		include $catalogPath;
	}
} catch (\Throwable $ex) {
	// If include fails, show a fallback link.
	echo '<div class="container py-4"><div class="alert alert-warning">Could not load catalog. <a href="/catalog">Open Catalog</a></div></div>';
}

