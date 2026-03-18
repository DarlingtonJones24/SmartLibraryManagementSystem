<div class="container py-5">
  <div class="row align-items-center mb-4">
    <div class="col-md-8">
      <h1 class="display-5">Welcome to the Library</h1>
      <p class="lead text-muted">Browse, borrow, and reserve books from our collection.</p>
    </div>
    <div class="col-md-4">
      <form class="d-flex" method="get" action="/catalog" role="search">
        <input class="form-control me-2" type="search" name="q" placeholder="Search the catalog" aria-label="Search">
        <button class="btn btn-primary" type="submit">Search</button>
      </form>
    </div>
  </div>

  <div class="row mb-4">
    <?php foreach ($homeViewModel->stats as $stat): ?>
      <div class="col-6 col-md-3 mb-2">
        <div class="card p-3 text-center">
          <div class="h4 mb-0"><?= htmlspecialchars($stat['value']) ?></div>
          <div class="text-muted small"><?= htmlspecialchars($stat['label']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h2 class="h4 mb-3">Featured Books</h2>
  <?php
  $catalogViewModel = $homeViewModel->catalog;
  $suppressCatalogHeader = true;
  $suppressCatalogContainer = true;
  include __DIR__ . '/../Books/index.php';
  ?>
</div>
