<?php
use App\Core\View;
use App\Core\Csrf;
/** @var string $content */
/** @var array $config */
$e = fn($v) => View::e($v);
$flashes = $flashes ?? [];
?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= $e($title ?? 'Panel') ?> · Liga Młodzieżowa PZSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;800;900&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=2">
    <link rel="stylesheet" href="/assets/css/admin.css?v=1">
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
</head>
<body class="admin-body">
<header class="admin-topbar">
    <div class="admin-topbar-inner">
        <a href="/admin" class="admin-brand">
            <img src="/assets/img/logo-pzss.svg" alt="" class="admin-logo">
            <span>Panel · Liga Młodzieżowa</span>
        </a>
        <nav class="admin-nav">
            <a href="/admin">Pulpit</a>
            <a href="/admin/news">Aktualności</a>
            <a href="/admin/kluby">Kluby</a>
            <a href="/admin/zawodnicy">Zawodnicy</a>
            <a href="/admin/rundy">Rundy &amp; wyniki</a>
            <a href="/admin/sponsorzy">Sponsorzy</a>
            <a href="/admin/import">Import CSV</a>
            <a href="/" target="_blank" rel="noopener">Strona ↗</a>
        </nav>
        <form method="post" action="/admin/logout" class="admin-logout">
            <?= Csrf::field() ?>
            <button class="btn btn-ghost-d">Wyloguj</button>
        </form>
    </div>
</header>

<main class="admin-main">
    <div class="admin-container">
        <?php foreach ($flashes as $f): ?>
            <div class="flash flash-<?= $e($f['type']) ?>"><?= $e($f['msg']) ?></div>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</main>
</body>
</html>
