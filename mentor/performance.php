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
    $pdo->beginTransaction();
    try {
        $attendanceStmt = $pdo->prepare('INSERT INTO attendance (student_id, percentage, recorded_at) VALUES (:student_id,:percentage,NOW())');
        $marksStmt = $pdo->prepare('INSERT INTO marks (student_id, subject, score, recorded_at) VALUES (:student_id,:subject,:score,NOW())');
        $attendanceStmt->execute([':student_id'=>(int)$_POST['student_id'],':percentage'=>(float)$_POST['attendance']]);
        $marksStmt->execute([':student_id'=>(int)$_POST['student_id'],':subject'=>cleanInput($_POST['subject']),':score'=>(float)$_POST['score']]);
        $pdo->commit();
        addAuditLog((int)$_SESSION['user']['id'],'Updated student performance');
    } catch (Throwable $th) {
        $pdo->rollBack();
    }
    redirect('/mentor/performance.php');
}
$studentsStmt = $pdo->prepare('SELECT s.id, u.full_name FROM mentor_assignment ma JOIN students s ON s.id=ma.student_id JOIN users u ON u.id=s.user_id WHERE ma.mentor_id=:mid');
$studentsStmt->execute([':mid'=>$mentorId]);
$students = $studentsStmt->fetchAll();
$pageTitle='Update Performance';
require __DIR__ . '/../includes/header.php';
?>
<div class="card"><div class="card-header">Attendance & Marks Update</div><div class="card-body">
<form method="post" class="row g-3">
<input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
<div class="col-md-4"><label>Student</label><select name="student_id" class="form-select" required><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['full_name']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label>Attendance %</label><input type="number" name="attendance" class="form-control" min="0" max="100" step="0.01" required></div>
<div class="col-md-3"><label>Subject</label><input type="text" name="subject" class="form-control" required></div>
<div class="col-md-2"><label>Score</label><input type="number" name="score" class="form-control" min="0" max="100" step="0.01" required></div>
<div class="col-md-1 d-flex align-items-end"><button class="btn btn-primary">Save</button></div>
</form></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
