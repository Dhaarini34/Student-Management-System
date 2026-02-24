<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$pdo = getPDO();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        die('Invalid CSRF token');
    }
    if (($_POST['action'] ?? '') === 'add') {
        $stmt = $pdo->prepare('INSERT INTO mentors (user_id, name, specialization, phone, created_at) VALUES (:user_id,:name,:specialization,:phone,NOW())');
        $stmt->execute([
            ':user_id' => (int) $_POST['user_id'],
            ':name' => cleanInput($_POST['name']),
            ':specialization' => cleanInput($_POST['specialization']),
            ':phone' => cleanInput($_POST['phone']),
        ]);
    }
    redirect('/admin/mentors.php');
}
$users = $pdo->query("SELECT id, full_name FROM users WHERE role='mentor' ORDER BY full_name")->fetchAll();
$mentors = $pdo->query('SELECT m.*, u.email FROM mentors m JOIN users u ON u.id=m.user_id ORDER BY m.name')->fetchAll();
$pageTitle = 'Manage Mentors';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between mb-3"><h4>Mentors</h4><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMentor">Add Mentor</button></div>
<div class="card"><div class="card-body table-responsive"><table class="table table-striped"><thead><tr><th>Name</th><th>Email</th><th>Specialization</th><th>Phone</th></tr></thead><tbody><?php foreach($mentors as $m): ?><tr><td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><?= e($m['specialization']) ?></td><td><?= e($m['phone']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="modal fade" id="addMentor"><div class="modal-dialog"><div class="modal-content"><form method="post"><div class="modal-header"><h5>Add Mentor</h5></div><div class="modal-body">
<input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>"><input type="hidden" name="action" value="add">
<div class="mb-2"><label>User</label><select name="user_id" class="form-select"><?php foreach($users as $u): ?><option value="<?= (int) $u['id'] ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="mb-2"><label>Name</label><input name="name" class="form-control" required></div>
<div class="mb-2"><label>Specialization</label><input name="specialization" class="form-control" required></div>
<div class="mb-2"><label>Phone</label><input name="phone" class="form-control" required></div>
</div><div class="modal-footer"><button class="btn btn-primary">Save</button></div></form></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
