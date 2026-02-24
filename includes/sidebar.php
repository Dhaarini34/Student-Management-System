<?php
declare(strict_types=1);
$role = $_SESSION['user']['role'] ?? '';
$menu = [
    'admin' => [
        ['Dashboard', '/admin/dashboard.php', 'bi-speedometer2'],
        ['Students', '/admin/students.php', 'bi-people'],
        ['Mentors', '/admin/mentors.php', 'bi-person-badge'],
        ['Assignments', '/admin/assignments.php', 'bi-diagram-3'],
        ['Risk Alerts', '/admin/risk_alerts.php', 'bi-exclamation-triangle'],
        ['Reports', '/admin/reports.php', 'bi-file-earmark-bar-graph'],
    ],
    'mentor' => [
        ['Dashboard', '/mentor/dashboard.php', 'bi-speedometer2'],
        ['Assigned Students', '/mentor/students.php', 'bi-people'],
        ['Counseling Sessions', '/mentor/sessions.php', 'bi-chat-dots'],
        ['Performance Update', '/mentor/performance.php', 'bi-journal-check'],
    ],
    'student' => [
        ['Dashboard', '/student/dashboard.php', 'bi-speedometer2'],
        ['Counseling History', '/student/history.php', 'bi-clock-history'],
    ],
];
?>
<aside class="sidebar bg-dark text-white p-3">
    <h5 class="mb-4">Mentoring</h5>
    <ul class="nav nav-pills flex-column gap-2">
        <?php foreach ($menu[$role] ?? [] as [$label, $path, $icon]): ?>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= BASE_URL . $path ?>"><i class="bi <?= e($icon) ?> me-2"></i><?= e($label) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
