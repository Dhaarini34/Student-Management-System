<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['mentor']);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id FROM mentors WHERE user_id=:uid LIMIT 1');
$stmt->execute([':uid' => (int) $_SESSION['user']['id']]);
$mentorId = (int) ($stmt->fetchColumn() ?: 0);
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        die('Invalid CSRF token');
    }
    $insert = $pdo->prepare('INSERT INTO counseling_sessions (mentor_id, student_id, session_date, notes, created_at) VALUES (:mentor_id,:student_id,:session_date,:notes,NOW())');
    $insert->execute([
        ':mentor_id' => $mentorId,
        ':student_id' => (int) $_POST['student_id'],
        ':session_date' => cleanInput($_POST['session_date']),
        ':notes' => cleanInput($_POST['notes']),
    ]);
    addAuditLog((int)$_SESSION['user']['id'],'Added counseling session');
    redirect('/mentor/sessions.php');
}
$studentsStmt = $pdo->prepare('SELECT s.id, u.full_name FROM mentor_assignment ma JOIN students s ON s.id=ma.student_id JOIN users u ON u.id=s.user_id WHERE ma.mentor_id=:mid');
$studentsStmt->execute([':mid'=>$mentorId]);
$students = $studentsStmt->fetchAll();
$listStmt = $pdo->prepare('SELECT cs.*, u.full_name student_name FROM counseling_sessions cs JOIN students s ON s.id=cs.student_id JOIN users u ON u.id=s.user_id WHERE cs.mentor_id=:mid ORDER BY cs.session_date DESC');
$listStmt->execute([':mid'=>$mentorId]);
$sessions = $listStmt->fetchAll();
$pageTitle='Counseling Sessions';
require __DIR__ . '/../includes/header.php';
?>
<div class="row g-3"><div class="col-lg-4"><div class="card"><div class="card-header">Add Session</div><div class="card-body"><form method="post">
<input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
<div class="mb-2"><label>Student</label><select name="student_id" class="form-select" required><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="mb-2"><label>Date</label><input type="date" name="session_date" class="form-control" required></div>
<div class="mb-2"><label>Notes</label><textarea name="notes" class="form-control" required></textarea></div>
<button class="btn btn-primary">Save Session</button></form></div></div></div>
<div class="col-lg-8"><div class="card"><div class="card-header">Session History</div><div class="card-body table-responsive"><table class="table"><thead><tr><th>Date</th><th>Student</th><th>Notes</th></tr></thead><tbody><?php foreach($sessions as $se): ?><tr><td><?= e($se['session_date']) ?></td><td><?= e($se['student_name']) ?></td><td><?= e($se['notes']) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
