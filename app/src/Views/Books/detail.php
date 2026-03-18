<?php $book = $bookDetailViewModel->book; ?>

<div class="container py-4">
  <div class="mb-3">
    <a href="/catalog" class="btn btn-sm btn-outline-secondary">Back to catalog</a>
  </div>

  <div class="card card-soft p-3 p-md-4">
    <div class="row g-4">
      <div class="col-md-4 col-lg-3">
        <img class="img-fluid rounded" src="<?= htmlspecialchars($book['coverPath']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" onerror="this.onerror=null;this.src='/assets/Uploads/covers/default-cover.svg'">
      </div>

      <div class="col-md-8 col-lg-9">
        <h2 class="h3 mb-2"><?= htmlspecialchars($book['title']) ?></h2>
        <div class="text-muted mb-3">Author: <?= htmlspecialchars($book['author']) ?></div>

        <div class="small text-muted mb-2">
          <?php if ($book['genre'] !== ''): ?>Genre: <?= htmlspecialchars($book['genre']) ?> &bull; <?php endif; ?>
          <?php if ($book['isbn'] !== ''): ?>ISBN: <?= htmlspecialchars($book['isbn']) ?> &bull; <?php endif; ?>
          <?php if ($book['publishedYear'] !== ''): ?>Published: <?= htmlspecialchars($book['publishedYear']) ?><?php endif; ?>
        </div>

        <div class="mb-3">
          <?php if ($book['availableCopies'] !== null): ?>
            <?php if ($book['availableCopies'] > 0): ?>
              <span class="badge badge-available">Available (<?= $book['availableCopies'] ?>)</span>
            <?php else: ?>
              <span class="badge badge-unavailable">Unavailable</span>
            <?php endif; ?>
          <?php elseif ($book['totalCopies'] !== null): ?>
            <span class="badge badge-info">Total copies: <?= $book['totalCopies'] ?></span>
          <?php endif; ?>
        </div>

        <p class="mb-0"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
      </div>
    </div>
  </div>
</div>
