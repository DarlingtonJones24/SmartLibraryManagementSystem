<?php
use App\Framework\TempData;
TempData::start();
$user = $user ?? [];
?>

<div class="container py-4">
    <h1 class="h3 mb-3">Edit Profile</h1>

    <div class="card p-4">
        <?php $flash = TempData::get('flash'); if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?>"><?= htmlspecialchars($flash['message'] ?? '') ?></div>
        <?php endif; ?>

        <form method="post" action="/profile/edit">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['Email'] ?? $user['email'] ?? '') ?>" required>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save Changes</button>
                <a class="btn btn-outline-secondary" href="/settings">Cancel</a>
            </div>
        </form>
    </div>
</div>
