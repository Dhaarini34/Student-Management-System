<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        die('Invalid CSRF token');
    }
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $stmt = $pdo->prepare('INSERT INTO students (user_id, student_number, department, semester, created_at) VALUES (:user_id,:student_number,:department,:semester,NOW())');
        $stmt->execute([
            ':user_id' => (int) $_POST['user_id'],
            ':student_number' => cleanInput($_POST['student_number']),
            ':department' => cleanInput($_POST['department']),
            ':semester' => (int) $_POST['semester'],
        ]);
        addAuditLog((int) $_SESSION['user']['id'], 'Added student profile');
    }
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM students WHERE id=:id');
        $stmt->execute([':id' => (int) $_POST['id']]);
    }
    redirect('/admin/students.php');
}

$users = $pdo->query("SELECT id, full_name FROM users WHERE role='student' ORDER BY full_name")->fetchAll();
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$total = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$students = $pdo->query("SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON u.id=s.user_id ORDER BY s.id DESC LIMIT {$limit} OFFSET {$offset}")->fetchAll();
$pageTitle = 'Manage Students';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Students</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add Student</button>
</div>
<div class="card"><div class="card-body table-responsive">
<table class="table table-striped">
<thead><tr><th>Name</th><th>Email</th><th>Student #</th><th>Department</th><th>Semester</th><th>Action</th></tr></thead>
<tbody><?php foreach ($students as $s): ?><tr>
<td><?= e($s['full_name']) ?></td><td><?= e($s['email']) ?></td><td><?= e($s['student_number']) ?></td><td><?= e($s['department']) ?></td><td><?= (int) $s['semester'] ?></td>
<td>
<form method="post" class="d-inline">
<input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
<button data-confirm="Delete student?" class="btn btn-sm btn-danger">Delete</button></form>
</td></tr><?php endforeach; ?></tbody></table>
</div></div>
<nav class="mt-3"><ul class="pagination"><?php for ($i=1; $i<=max(1, (int)ceil($total/$limit)); $i++): ?><li class="page-item <?= $i===$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?></ul></nav>

<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content"><form method="post">
<div class="modal-header"><h5>Add Student</h5></div><div class="modal-body">
<input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>"><input type="hidden" name="action" value="add">
<div class="mb-2"><label>User</label><select name="user_id" class="form-select" required><?php foreach($users as $u): ?><option value="<?= (int) $u['id'] ?>"><?= e($u['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="mb-2"><label>Student Number</label><input name="student_number" class="form-control" required></div>
<div class="mb-2"><label>Department</label><input name="department" class="form-control" required></div>
<div class="mb-2"><label>Semester</label><input name="semester" type="number" min="1" max="12" class="form-control" required></div>
</div><div class="modal-footer"><button class="btn btn-primary">Save</button></div>
</form></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
