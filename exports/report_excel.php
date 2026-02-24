<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$rows = getPDO()->query('SELECT student_name, attendance_percent, average_mark, risk_status FROM student_risk_view ORDER BY student_name')->fetchAll();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="mentoring_report.xls"');
echo "Student\tAttendance\tAverage Mark\tRisk\n";
foreach ($rows as $r) {
    echo implode("\t", [$r['student_name'], $r['attendance_percent'], $r['average_mark'], $r['risk_status']]) . "\n";
}
