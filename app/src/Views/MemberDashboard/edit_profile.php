<div class="container py-4">
  <h1 class="h3 mb-3"><?= htmlspecialchars($profileViewModel->title) ?></h1>

  <div class="card p-4">
    <?php if (!empty($message)): ?>
      <div class="alert alert-<?= htmlspecialchars($message['type'] ?? 'info') ?>"><?= htmlspecialchars($message['text'] ?? '') ?></div>
    <?php endif; ?>

    <form method="post" action="/profile/edit">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($profileViewModel->name) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profileViewModel->email) ?>" required>
      </div>

      <div class="mt-3">
        <button class="btn btn-primary">Save Changes</button>
        <a class="btn btn-outline-secondary" href="/settings">Cancel</a>
      </div>
    </form>
  </div>
</div>
