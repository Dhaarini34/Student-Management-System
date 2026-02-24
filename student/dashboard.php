<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['student']);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT s.id, s.student_number, s.department, s.semester FROM students s WHERE s.user_id=:uid LIMIT 1');
$stmt->execute([':uid'=>(int)$_SESSION['user']['id']]);
$student = $stmt->fetch();
if (!$student) {
    die('Student profile missing.');
}
$mentorStmt = $pdo->prepare('SELECT m.name, m.specialization, m.phone FROM mentor_assignment ma JOIN mentors m ON m.id=ma.mentor_id WHERE ma.student_id=:sid LIMIT 1');
$mentorStmt->execute([':sid'=>(int)$student['id']]);
$mentor = $mentorStmt->fetch();
$riskStmt = $pdo->prepare('SELECT attendance_percent, average_mark, risk_status FROM student_risk_view WHERE student_id=:sid');
$riskStmt->execute([':sid'=>(int)$student['id']]);
$risk = $riskStmt->fetch() ?: ['attendance_percent'=>0,'average_mark'=>0,'risk_status'=>'HIGH'];
$pageTitle='Student Dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
<div class="col-lg-4"><div class="card"><div class="card-body"><h6>Attendance</h6><h3><?= e((string)$risk['attendance_percent']) ?>%</h3></div></div></div>
<div class="col-lg-4"><div class="card"><div class="card-body"><h6>Average Mark</h6><h3><?= e((string)$risk['average_mark']) ?></h3></div></div></div>
<div class="col-lg-4"><div class="card"><div class="card-body"><h6>Risk Status</h6><h3 class="risk-<?= e($risk['risk_status']) ?>"><?= e($risk['risk_status']) ?></h3></div></div></div>
</div>
<div class="card mt-3"><div class="card-header">Mentor Details</div><div class="card-body">
<?php if ($mentor): ?>
<p><strong>Name:</strong> <?= e($mentor['name']) ?></p>
<p><strong>Specialization:</strong> <?= e($mentor['specialization']) ?></p>
<p><strong>Phone:</strong> <?= e($mentor['phone']) ?></p>
<?php else: ?><p class="text-muted">No mentor assigned yet.</p><?php endif; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
