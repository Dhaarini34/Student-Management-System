<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['mentor']);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id FROM mentors WHERE user_id=:uid LIMIT 1');
$stmt->execute([':uid' => (int) $_SESSION['user']['id']]);
$mentorId = (int) ($stmt->fetchColumn() ?: 0);
$listStmt = $pdo->prepare('SELECT s.id student_id, u.full_name, s.student_number, rv.attendance_percent, rv.average_mark, rv.risk_status FROM mentor_assignment ma JOIN students s ON s.id=ma.student_id JOIN users u ON u.id=s.user_id LEFT JOIN student_risk_view rv ON rv.student_id=s.id WHERE ma.mentor_id=:mid ORDER BY u.full_name');
$listStmt->execute([':mid'=>$mentorId]);
$students=$listStmt->fetchAll();
$pageTitle='Assigned Students';
require __DIR__ . '/../includes/header.php';
?>
<h4>Assigned Students</h4>
<div class="card"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Name</th><th>Student #</th><th>Attendance</th><th>Avg Marks</th><th>Risk</th></tr></thead><tbody><?php foreach($students as $s): ?><tr><td><?= e($s['full_name']) ?></td><td><?= e($s['student_number']) ?></td><td><?= e((string)$s['attendance_percent']) ?></td><td><?= e((string)$s['average_mark']) ?></td><td class="risk-<?= e($s['risk_status']) ?>"><?= e($s['risk_status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
