<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$rows = getPDO()->query('SELECT student_name, attendance_percent, average_mark, risk_status FROM student_risk_view ORDER BY student_name')->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Printable Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4" onload="window.print()">
<h3>Student Mentoring Report</h3>
<table class="table table-bordered"><thead><tr><th>Student</th><th>Attendance</th><th>Mark</th><th>Risk</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= e($r['student_name']) ?></td><td><?= e((string)$r['attendance_percent']) ?></td><td><?= e((string)$r['average_mark']) ?></td><td><?= e($r['risk_status']) ?></td></tr><?php endforeach; ?>
</tbody></table></body></html>
