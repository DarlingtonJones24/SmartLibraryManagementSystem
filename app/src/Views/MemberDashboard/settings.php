<div class="container py-4">
  <h1 class="h3 mb-3"><?= htmlspecialchars($profileViewModel->title) ?></h1>

  <div class="card p-4">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <div><?= htmlspecialchars($profileViewModel->name) ?></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <div><?= htmlspecialchars($profileViewModel->email) ?></div>
    </div>

    <div class="mt-3">
      <a class="btn btn-primary" href="/profile/edit">Edit Profile</a>
      <a class="btn btn-outline-secondary" href="/change-password">Change Password</a>
    </div>
  </div>
</div>
