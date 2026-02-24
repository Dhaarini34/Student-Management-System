<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$pageTitle='Reports';
require __DIR__ . '/../includes/header.php';
?>
<div class="card"><div class="card-body">
<h4>Export Reports</h4>
<p class="text-muted">Generate student mentoring performance reports.</p>
<a class="btn btn-success me-2" href="<?= BASE_URL ?>/exports/report_excel.php">Export Excel (.xls)</a>
<a class="btn btn-danger me-2" href="<?= BASE_URL ?>/exports/report_pdf.php" target="_blank">Export PDF (TCPDF)</a>
<a class="btn btn-secondary" href="<?= BASE_URL ?>/exports/printable_report.php" target="_blank">Printable Report</a>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
