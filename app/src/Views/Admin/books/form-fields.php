<div class="mb-3">
  <label class="form-label">Title</label>
  <input type="text" name="Title" class="form-control" value="<?= htmlspecialchars($book['Title'] ?? '') ?>" required>
</div>

<div class="mb-3">
  <label class="form-label">Author</label>
  <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book['author'] ?? '') ?>" required>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">ISBN</label>
    <input type="text" name="ISBN" class="form-control" value="<?= htmlspecialchars($book['ISBN'] ?? '') ?>">
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Genre</label>
    <input type="text" name="Genre" class="form-control" value="<?= htmlspecialchars($book['Genre'] ?? '') ?>">
  </div>
  <div class="col-md-4 mb-3">
    <label class="form-label">Published Year</label>
    <input type="number" name="published_year" class="form-control" value="<?= htmlspecialchars($book['published_year'] ?? '') ?>">
  </div>
</div>

<div class="mb-3">
  <label class="form-label">Cover URL / filename</label>
  <input type="text" name="cover_url" class="form-control" value="<?= htmlspecialchars($book['cover_url'] ?? '') ?>" placeholder="e.g. covers/my-cover.jpg">
</div>

<div class="mb-3">
  <label class="form-label">Total Copies</label>
  <input type="number" name="total_copies" class="form-control" min="1" value="<?= htmlspecialchars($book['total_copies'] ?? 1) ?>" required>
</div>

<div class="mb-3">
  <label class="form-label">Description</label>
  <textarea name="Description" class="form-control" rows="4"><?= htmlspecialchars($book['Description'] ?? '') ?></textarea>
</div>
