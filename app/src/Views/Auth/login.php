<div class="auth-page">
  <div class="auth-grid">
    <div class="auth-hero" style="background-image: url('/assets/Uploads/otherImages/loginImage.jpg')" aria-hidden="true"></div>

    <div class="auth-panel">
      <div class="auth-card">
        <h2 class="auth-title">Login</h2>
        <p class="auth-sub">Enter your credentials to access your account</p>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/login">
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input class="form-control" name="email" type="email" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input class="form-control" name="password" type="password" required>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-primary w-100">Login</button>
          </div>

          <p class="text-muted small mt-3 mb-0">
            Don't have an account? <a href="/register">Register now</a>
          </p>
        </form>

        <div class="demo-box">
          <strong>Need help?</strong> For account or borrowing assistance, contact your library at <a href="mailto:help@yourlibrary.org">help@yourlibrary.org</a> or visit the <a href="https://www.rodi.nl/haarlem/cultuur/328441/bibliotheek-haarlem-alle-locaties-parkeren-en-boeken-lenen" target="_blank" rel="noopener">Help Center</a>.
        </div>
      </div>
    </div>
  </div>
</div>
