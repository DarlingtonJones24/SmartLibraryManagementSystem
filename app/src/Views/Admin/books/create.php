<div class="container py-4">
  <h3 class="mb-3">Add Book</h3>

  <div class="card card-soft p-4">
    <form method="post" action="/index.php">
      <input type="hidden" name="route" value="admin/books/create">
      <?php include __DIR__ . '/form-fields.php'; ?>
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="/index.php?route=admin/books">Cancel</a>
    </form>
  </div>
</div>
