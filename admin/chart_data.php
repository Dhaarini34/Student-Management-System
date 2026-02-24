<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
header('Content-Type: application/json');
$pdo = getPDO();

$riskRows = $pdo->query('SELECT risk_status, COUNT(*) total FROM student_risk_view GROUP BY risk_status')->fetchAll();
$attendanceRows = $pdo->query('SELECT DATE(recorded_at) day, ROUND(AVG(percentage),2) avg_attendance FROM attendance GROUP BY DATE(recorded_at) ORDER BY day DESC LIMIT 7')->fetchAll();
$mentorRows = $pdo->query('SELECT m.name mentor_name, ROUND(AVG(mk.score),2) avg_score FROM mentors m JOIN mentor_assignment ma ON ma.mentor_id=m.id JOIN marks mk ON mk.student_id=ma.student_id GROUP BY m.id')->fetchAll();

echo json_encode([
    'risk' => [
        'labels' => array_column($riskRows, 'risk_status'),
        'values' => array_map('intval', array_column($riskRows, 'total')),
    ],
    'attendance' => [
        'labels' => array_reverse(array_column($attendanceRows, 'day')),
        'values' => array_reverse(array_map('floatval', array_column($attendanceRows, 'avg_attendance'))),
    ],
    'mentor' => [
        'labels' => array_column($mentorRows, 'mentor_name'),
        'values' => array_map('floatval', array_column($mentorRows, 'avg_score')),
    ],
], JSON_THROW_ON_ERROR);
