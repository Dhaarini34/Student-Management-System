<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
requireLogin();
?>
<!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
<div class="text-center">
    <h1 class="text-danger">403</h1>
    <p>You are not authorized to access this page.</p>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Back to Dashboard</a>
</div>
</body></html>
