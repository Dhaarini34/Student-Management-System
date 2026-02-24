<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
requireRole(['admin']);
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';

$counts = getDashboardCounts();
?>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Students</h6><h3><?= $counts['students'] ?></h3></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Mentors</h6><h3><?= $counts['mentors'] ?></h3></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>Sessions</h6><h3><?= $counts['sessions'] ?></h3></div></div></div>
    <div class="col-md-3"><div class="card card-stat"><div class="card-body"><h6>High Risk</h6><h3 class="text-danger"><?= $counts['high_risk'] ?></h3></div></div></div>
</div>
<div class="row g-3">
    <div class="col-lg-4"><div class="card"><div class="card-header">Risk Distribution</div><div class="card-body"><canvas id="riskChart"></canvas></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-header">Attendance Trends</div><div class="card-body"><canvas id="attendanceChart"></canvas></div></div></div>
    <div class="col-lg-4"><div class="card"><div class="card-header">Mentor Comparison</div><div class="card-body"><canvas id="mentorChart"></canvas></div></div></div>
</div>
<script>
fetch('<?= BASE_URL ?>/admin/chart_data.php').then(r=>r.json()).then(data=>{
    new Chart(document.getElementById('riskChart'), {type:'pie', data:{labels:data.risk.labels, datasets:[{data:data.risk.values}]}});
    new Chart(document.getElementById('attendanceChart'), {type:'line', data:{labels:data.attendance.labels, datasets:[{label:'Attendance %', data:data.attendance.values}]}});
    new Chart(document.getElementById('mentorChart'), {type:'bar', data:{labels:data.mentor.labels, datasets:[{label:'Avg Marks', data:data.mentor.values}]}});
});
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
