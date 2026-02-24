<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$rows = getPDO()->query('SELECT * FROM student_risk_view ORDER BY FIELD(risk_status, "HIGH","MEDIUM","SAFE"), student_name')->fetchAll();
$pageTitle='Risk Alerts';
require __DIR__ . '/../includes/header.php';
?>
<h4 class="mb-3">Student Risk Monitoring</h4>
<div class="card"><div class="card-body table-responsive"><table class="table table-bordered"><thead><tr><th>Student</th><th>Attendance %</th><th>Avg Marks</th><th>Last Counseling</th><th>Risk</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr>
<td><?= e($r['student_name']) ?></td><td><?= e((string)$r['attendance_percent']) ?></td><td><?= e((string)$r['average_mark']) ?></td><td><?= e($r['last_counseling'] ?? '-') ?></td>
<td class="risk-<?= e($r['risk_status']) ?>"><?= e($r['risk_status']) ?></td>
</tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
