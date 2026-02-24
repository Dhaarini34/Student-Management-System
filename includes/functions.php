<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('/auth/login.php');
    }
}

function requireRole(array $roles): void
{
    requireLogin();
    $role = $_SESSION['user']['role'] ?? '';
    if (!in_array($role, $roles, true)) {
        redirect('/unauthorized.php');
    }
}

function cleanInput(?string $value): string
{
    return trim((string) $value);
}

function calculateRiskStatus(float $attendancePercent, float $averageMark, ?string $lastCounselingDate): string
{
    $lastDate = $lastCounselingDate ? new DateTime($lastCounselingDate) : null;
    $daysSinceCounseling = $lastDate ? (new DateTime())->diff($lastDate)->days : 999;

    if ($attendancePercent < 75.0 || $averageMark < 50.0 || $daysSinceCounseling > 30) {
        return 'HIGH';
    }

    if ($attendancePercent >= 75.0 && $attendancePercent <= 85.0) {
        return 'MEDIUM';
    }

    return 'SAFE';
}

function addAuditLog(int $userId, string $action): void
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, created_at) VALUES (:user_id, :action, NOW())');
    $stmt->execute([
        ':user_id' => $userId,
        ':action' => $action,
    ]);
}

function getDashboardCounts(): array
{
    $pdo = getPDO();
    $counts = [];
    $counts['students'] = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
    $counts['mentors'] = (int) $pdo->query('SELECT COUNT(*) FROM mentors')->fetchColumn();
    $counts['sessions'] = (int) $pdo->query('SELECT COUNT(*) FROM counseling_sessions')->fetchColumn();
    $counts['high_risk'] = (int) $pdo->query("SELECT COUNT(*) FROM student_risk_view WHERE risk_status = 'HIGH'")->fetchColumn();
    return $counts;
}
