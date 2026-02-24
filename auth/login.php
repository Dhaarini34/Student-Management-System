<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid CSRF token.';
    } else {
        $email = cleanInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = getPDO()->prepare('SELECT id, full_name, email, password, role FROM users WHERE email = :email AND is_active = 1 LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];
            addAuditLog((int) $user['id'], 'User logged in');
            redirect('/index.php');
        }
        $error = 'Invalid email or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?= e(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="card shadow-sm" style="width:420px;">
    <div class="card-body p-4">
        <h4 class="mb-3 text-center">Mentoring System Login</h4>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <button class="btn btn-primary w-100" type="submit">Sign In</button>
        </form>
    </div>
</div>
</body>
</html>
