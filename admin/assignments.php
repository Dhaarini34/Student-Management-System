<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$pdo = getPDO();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        die('Invalid CSRF token');
    }
    $stmt = $pdo->prepare('INSERT INTO mentor_assignment (mentor_id, student_id, assigned_at) VALUES (:mentor_id,:student_id,NOW()) ON DUPLICATE KEY UPDATE mentor_id=VALUES(mentor_id), assigned_at=NOW()');
    $stmt->execute([
        ':mentor_id' => (int) $_POST['mentor_id'],
        ':student_id' => (int) $_POST['student_id'],
    ]);
    addAuditLog((int) $_SESSION['user']['id'], 'Updated mentor assignment');
    redirect('/admin/assignments.php');
}
$mentors = $pdo->query('SELECT id, name FROM mentors ORDER BY name')->fetchAll();
$students = $pdo->query('SELECT s.id, u.full_name FROM students s JOIN users u ON u.id=s.user_id ORDER BY u.full_name')->fetchAll();
$list = $pdo->query('SELECT ma.id, m.name mentor_name, u.full_name student_name, ma.assigned_at FROM mentor_assignment ma JOIN mentors m ON m.id=ma.mentor_id JOIN students s ON s.id=ma.student_id JOIN users u ON u.id=s.user_id ORDER BY ma.assigned_at DESC')->fetchAll();
$pageTitle='Mentor Assignments';
require __DIR__ . '/../includes/header.php';
?>
<div class="row g-3"><div class="col-lg-4"><div class="card"><div class="card-header">Assign Mentor</div><div class="card-body"><form method="post">
<input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
<div class="mb-2"><label>Student</label><select name="student_id" class="form-select" required><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="mb-2"><label>Mentor</label><select name="mentor_id" class="form-select" required><?php foreach($mentors as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['name']) ?></option><?php endforeach; ?></select></div>
<button class="btn btn-primary">Assign</button>
</form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Assignment List</div><div class="card-body table-responsive"><table class="table"><thead><tr><th>Student</th><th>Mentor</th><th>Assigned At</th></tr></thead><tbody><?php foreach($list as $r): ?><tr><td><?= e($r['student_name']) ?></td><td><?= e($r['mentor_name']) ?></td><td><?= e($r['assigned_at']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
