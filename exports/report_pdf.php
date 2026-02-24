<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$tcpdfPath = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
if (!file_exists($tcpdfPath)) {
    http_response_code(500);
    echo 'TCPDF not found. Run: composer require tecnickcom/tcpdf';
    exit;
}
require_once $tcpdfPath;
$rows = getPDO()->query('SELECT student_name, attendance_percent, average_mark, risk_status FROM student_risk_view ORDER BY student_name')->fetchAll();
$pdf = new TCPDF();
$pdf->SetCreator('Mentoring System');
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 11);
$html = '<h2>Student Mentoring Report</h2><table border="1" cellpadding="4"><tr><th><b>Student</b></th><th><b>Attendance</b></th><th><b>Mark</b></th><th><b>Risk</b></th></tr>';
foreach ($rows as $r) {
    $html .= '<tr><td>' . e($r['student_name']) . '</td><td>' . e((string)$r['attendance_percent']) . '</td><td>' . e((string)$r['average_mark']) . '</td><td>' . e($r['risk_status']) . '</td></tr>';
}
$html .= '</table>';
$pdf->writeHTML($html);
$pdf->Output('mentoring_report.pdf', 'I');
