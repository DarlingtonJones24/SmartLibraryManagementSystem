<div class="auth-page">
  <div class="auth-grid">
    <div class="auth-hero" style="background-image: url('/assets/Uploads/otherImages/loginImage.jpg')" aria-hidden="true"></div>
    <div class="auth-panel">
      <div class="auth-card">
        <h2 class="auth-title">Create account</h2>
        <p class="auth-sub">Register a new account to access the library.</p>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= htmlspecialchars($message['type'] ?? 'info') ?>"><?= htmlspecialchars($message['text'] ?? '') ?></div>
        <?php endif; ?>

        <form method="post" action="/register">
          <div class="mb-3">
            <label class="form-label">Full name</label>
            <input class="form-control" name="name" type="text" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input class="form-control" name="email" type="email" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input class="form-control" name="password2" type="password" required>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary">Register</button>
            <a href="/login" class="small">Already have an account?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
