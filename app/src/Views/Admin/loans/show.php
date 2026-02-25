<?php
$loan = $loan ?? [];

$title = (string)($loan['Title'] ?? 'Loan');
$author = (string)($loan['author'] ?? 'Unknown');
$isbn = (string)($loan['ISBN'] ?? '');
$genre = (string)($loan['Genre'] ?? '');
$publishedYear = (string)($loan['published_year'] ?? '');
$description = (string)($loan['Description'] ?? 'No description available.');

$borrower = (string)($loan['user_name'] ?? '');
$borrowerEmail = (string)($loan['user_email'] ?? '');
$loanedAt = (string)($loan['loaned_at'] ?? '');
$dueAt = (string)($loan['due_at'] ?? '');
$returnedAt = (string)($loan['returned_at'] ?? '');

$cover = trim((string)($loan['cover_url'] ?? ''));
if ($cover === '') {
		$coverPath = '/assets/Uploads/covers/default-cover.svg';
} else {
		$coverPath = preg_match('#(^/|assets/Uploads/)#i', $cover)
				? '/' . ltrim($cover, '/')
				: '/assets/Uploads/covers/' . rawurlencode($cover);
}
?>

<div class="container py-4">
	<div class="mb-3 d-flex justify-content-between align-items-center">
		<h3 class="mb-0">Loan Details</h3>
		<a href="/admin/loans" class="btn btn-sm btn-outline-secondary">Back to loans</a>
	</div>

	<div class="card card-soft p-3 p-md-4">
		<div class="row g-4">
			<div class="col-md-4 col-lg-3">
				<img class="img-fluid rounded" src="<?= htmlspecialchars($coverPath) ?>" alt="<?= htmlspecialchars($title) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
			</div>

			<div class="col-md-8 col-lg-9">
				<h2 class="h4 mb-2"><?= htmlspecialchars($title) ?></h2>
				<div class="text-muted mb-3">Author: <?= htmlspecialchars($author) ?></div>

				<div class="small text-muted mb-3">
					<?php if ($genre !== ''): ?>Genre: <?= htmlspecialchars($genre) ?> • <?php endif; ?>
					<?php if ($isbn !== ''): ?>ISBN: <?= htmlspecialchars($isbn) ?> • <?php endif; ?>
					<?php if ($publishedYear !== ''): ?>Published: <?= htmlspecialchars($publishedYear) ?><?php endif; ?>
				</div>

				<div class="mb-2"><strong>Borrower:</strong> <?= htmlspecialchars($borrower) ?></div>
				<?php if ($borrowerEmail !== ''): ?>
					<div class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($borrowerEmail) ?></div>
				<?php endif; ?>
				<div class="mb-2"><strong>Loaned At:</strong> <?= htmlspecialchars($loanedAt) ?></div>
				<div class="mb-2"><strong>Due At:</strong> <?= htmlspecialchars($dueAt) ?></div>
				<?php if ($returnedAt !== ''): ?>
					<div class="mb-3"><strong>Returned At:</strong> <?= htmlspecialchars($returnedAt) ?></div>
				<?php endif; ?>

				<p class="mb-0"><?= nl2br(htmlspecialchars($description)) ?></p>
			</div>
		</div>
	</div>
</div>
