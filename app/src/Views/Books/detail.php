<?php
$book = $book ?? [];

$title = (string)($book['Title'] ?? $book['title'] ?? 'Book');
$author = (string)($book['author'] ?? $book['Author'] ?? 'Unknown');
$genre = (string)($book['Genre'] ?? $book['genre'] ?? '');
$isbn = (string)($book['ISBN'] ?? $book['isbn'] ?? '');
$publishedYear = (string)($book['published_year'] ?? $book['publishedYear'] ?? '');
$description = (string)($book['Description'] ?? $book['description'] ?? 'No description available.');

$available = $book['available'] ?? $book['available_count'] ?? $book['AvailableCopies'] ?? null;
$totalCopies = $book['total_copies'] ?? $book['totalCopies'] ?? $book['TotalCopies'] ?? null;

$cover = trim((string)($book['cover_url'] ?? $book['cover'] ?? ''));
if ($cover === '') {
		$coverPath = '/assets/Uploads/covers/default-cover.svg';
} else {
		$coverPath = preg_match('#(^/|assets/Uploads/)#i', $cover)
				? '/' . ltrim($cover, '/')
				: '/assets/Uploads/covers/' . rawurlencode($cover);
}
?>

<div class="container py-4">
	<div class="mb-3">
		<a href="/admin/reservation" class="btn btn-sm btn-outline-secondary">Back to reservations</a>
	</div>

	<div class="card card-soft p-3 p-md-4">
		<div class="row g-4">
			<div class="col-md-4 col-lg-3">
				<img class="img-fluid rounded" src="<?= htmlspecialchars($coverPath) ?>" alt="<?= htmlspecialchars($title) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
			</div>

			<div class="col-md-8 col-lg-9">
				<h2 class="h3 mb-2"><?= htmlspecialchars($title) ?></h2>
				<div class="text-muted mb-3">Author: <?= htmlspecialchars($author) ?></div>

				<div class="small text-muted mb-2">
					<?php if ($genre !== ''): ?>Genre: <?= htmlspecialchars($genre) ?> • <?php endif; ?>
					<?php if ($isbn !== ''): ?>ISBN: <?= htmlspecialchars($isbn) ?> • <?php endif; ?>
					<?php if ($publishedYear !== ''): ?>Published: <?= htmlspecialchars($publishedYear) ?><?php endif; ?>
				</div>

				<div class="mb-3">
					<?php if ($available !== null): ?>
						<?php if ((int)$available > 0): ?>
							<span class="badge badge-available">Available (<?= (int)$available ?>)</span>
						<?php else: ?>
							<span class="badge badge-unavailable">Unavailable</span>
						<?php endif; ?>
					<?php elseif ($totalCopies !== null): ?>
						<span class="badge badge-info">Total copies: <?= (int)$totalCopies ?></span>
					<?php endif; ?>
				</div>

				<p class="mb-0"><?= nl2br(htmlspecialchars($description)) ?></p>
			</div>
		</div>
	</div>
</div>
