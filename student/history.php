<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['student']);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id FROM students WHERE user_id=:uid LIMIT 1');
$stmt->execute([':uid'=>(int)$_SESSION['user']['id']]);
$studentId = (int) ($stmt->fetchColumn() ?: 0);
$histStmt = $pdo->prepare('SELECT cs.session_date, cs.notes, m.name mentor_name FROM counseling_sessions cs JOIN mentors m ON m.id=cs.mentor_id WHERE cs.student_id=:sid ORDER BY cs.session_date DESC');
$histStmt->execute([':sid'=>$studentId]);
$history=$histStmt->fetchAll();
$pageTitle='Counseling History';
require __DIR__ . '/../includes/header.php';
?>
<h4>Counseling History</h4>
<div class="card"><div class="card-body table-responsive"><table class="table table-striped"><thead><tr><th>Date</th><th>Mentor</th><th>Notes</th></tr></thead><tbody><?php foreach($history as $h): ?><tr><td><?= e($h['session_date']) ?></td><td><?= e($h['mentor_name']) ?></td><td><?= e($h['notes']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
