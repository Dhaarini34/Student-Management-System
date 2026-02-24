<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['mentor']);
$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id, name FROM mentors WHERE user_id=:uid LIMIT 1');
$stmt->execute([':uid' => (int) $_SESSION['user']['id']]);
$mentor = $stmt->fetch();
if (!$mentor) {
    die('Mentor profile missing.');
}
$summaryStmt = $pdo->prepare('SELECT COUNT(*) total_students FROM mentor_assignment WHERE mentor_id=:mid');
$summaryStmt->execute([':mid' => (int) $mentor['id']]);
$totalStudents = (int) $summaryStmt->fetchColumn();
$sessionStmt = $pdo->prepare('SELECT COUNT(*) FROM counseling_sessions WHERE mentor_id=:mid');
$sessionStmt->execute([':mid' => (int) $mentor['id']]);
$totalSessions = (int) $sessionStmt->fetchColumn();
$pageTitle='Mentor Dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
<div class="col-md-6"><div class="card"><div class="card-body"><h6>Assigned Students</h6><h3><?= $totalStudents ?></h3></div></div></div>
<div class="col-md-6"><div class="card"><div class="card-body"><h6>Total Counseling Sessions</h6><h3><?= $totalSessions ?></h3></div></div></div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
