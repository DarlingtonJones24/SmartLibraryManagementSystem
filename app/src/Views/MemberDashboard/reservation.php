<?php
use App\Framework\Auth;
\App\Framework\TempData::start();
$user = Auth::user();
?>

<div class="container py-4">
	<div class="d-flex justify-content-between align-items-start mb-3">
		<div>
			<h1 class="display-6 mb-1">My Reservations</h1>
			<div class="text-muted">View and manage your book reservations.</div>
		</div>
	</div>

	<?php
	$reservations = $reservations ?? [];

	if (empty($reservations)): ?>
		<div class="card p-4 text-muted">You have no reservations.</div>
	<?php else: ?>
		<div class="list-group">
			<?php foreach ($reservations as $r):
				$title = $r['Title'] ?? '';
				$author = $r['author'] ?? '';
				$cover = $r['cover_url'] ?? $r['cover'] ?? '';
				if (trim($cover) === '') {
					$coverPath = '/assets/Uploads/covers/default-cover.svg';
				} else {
					$coverPath = preg_match('#(^/|assets/Uploads/)#i', $cover) ? '/' . ltrim($cover, '/') : '/assets/Uploads/covers/' . rawurlencode($cover);
				}

				// Map DB status to label and badge class
				$rawStatus = strtolower(trim($r['Status'] ?? ''));
				$statusLabel = $rawStatus === 'waiting' ? 'pending' : ($rawStatus === 'ready' ? 'ready' : $rawStatus);
				$statusClass = $rawStatus === 'waiting' ? 'badge-warning' : ($rawStatus === 'ready' ? 'badge-success' : 'badge-secondary');

				$created = !empty($r['created_at']) ? date('Y-m-d', strtotime($r['created_at'])) : '';
				$expires = !empty($r['expires_at']) ? date('Y-m-d', strtotime($r['expires_at'])) : '';
			?>

			<div class="card mb-3 p-3">
				<div class="d-flex align-items-center">
					<div style="width:72px;flex:0 0 72px;margin-right:16px">
						<img src="<?= $coverPath ?>" alt="<?= htmlspecialchars($title) ?>" class="img-fluid rounded" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
					</div>
					<div class="flex-grow-1">
						<div class="fw-bold fs-5"><?= htmlspecialchars($title) ?></div>
						<div class="small text-muted"><?= htmlspecialchars($author) ?></div>
						<div class="small text-muted mt-2">Reserved: <?= htmlspecialchars($created) ?><?php if ($expires): ?> • Expires: <?= htmlspecialchars($expires) ?><?php endif; ?></div>
					</div>
					<div class="text-end">
						<span class="badge <?= $statusClass ?> rounded-pill text-uppercase" style="padding:.45rem .6rem;margin-right:.6rem;"><?= htmlspecialchars($statusLabel) ?></span>
						<?php if ($rawStatus === 'ready'): ?>
							<a href="/books/<?= (int)($r['book_id'] ?? $r['bookId'] ?? 0) ?>" class="btn btn-accent">Pickup</a>
						<?php else: ?>
							<form method="post" action="/reserve/cancel" class="d-inline">
								<input type="hidden" name="reservation_id" value="<?= (int)($r['id'] ?? 0) ?>">
								<input type="hidden" name="book_id" value="<?= (int)($r['book_id'] ?? $r['bookId'] ?? 0) ?>">
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
