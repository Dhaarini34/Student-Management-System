<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    $role = $_SESSION['user']['role'];
    $map = [
        'admin' => '/admin/dashboard.php',
        'mentor' => '/mentor/dashboard.php',
        'student' => '/student/dashboard.php',
    ];
    redirect($map[$role] ?? '/auth/login.php');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body p-4 p-md-5">
            <h2 class="mb-3">Student Mentoring Management System</h2>
            <p class="text-muted mb-4">If you just imported the project and want to see output, start here. Use the login button below and sign in with one of the demo accounts.</p>

            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="border rounded p-3 bg-white h-100"><h6>Admin</h6><div class="small">admin@college.edu</div><div class="small">Admin@123</div></div></div>
                <div class="col-md-4"><div class="border rounded p-3 bg-white h-100"><h6>Mentor</h6><div class="small">mentor1@college.edu</div><div class="small">Mentor@123</div></div></div>
                <div class="col-md-4"><div class="border rounded p-3 bg-white h-100"><h6>Student</h6><div class="small">student1@college.edu</div><div class="small">Student@123</div></div></div>
            </div>

            <a href="<?= BASE_URL ?>/auth/login.php" class="btn btn-primary">Open Login Page</a>
            <a href="<?= BASE_URL ?>/README.md" class="btn btn-outline-secondary ms-2">Read Setup Guide</a>
        </div>
    </div>
</div>
</body>
</html>
