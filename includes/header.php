<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
requireLogin();
$user = $_SESSION['user'];
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="main-content flex-grow-1">
        <nav class="navbar navbar-expand navbar-light bg-white border-bottom px-3">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1"><?= e(APP_NAME) ?></span>
                <div class="ms-auto d-flex align-items-center gap-3">
                    <span class="text-muted small">Welcome, <?= e($user['full_name']) ?> (<?= e(strtoupper($user['role'])) ?>)</span>
                    <a class="btn btn-outline-danger btn-sm" href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
                </div>
            </div>
        </nav>
        <div class="container-fluid p-4">
