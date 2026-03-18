<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Library') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/site.css">
  <link rel="stylesheet" href="/css/auth.css">
  <link rel="stylesheet" href="/css/catalog.css">
  <?php
  // Load admin.css only on admin pages
  $route = $_GET['route'] ?? '';
  $isAdminPath = (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin') === 0) || (strpos($route, 'admin/') === 0);
  if ($isAdminPath):
  ?>
    <link rel="stylesheet" href="/css/admin.css">
  <?php endif; ?>
</head>
<body>

<main>
  <?php if (!empty($message)): ?>
    <div class="container mt-3">
      <div class="alert alert-<?= htmlspecialchars($message['type'] ?? 'info') ?>" role="alert">
        <?= htmlspecialchars($message['text'] ?? '') ?>
      </div>
    </div>
  <?php endif; ?>
