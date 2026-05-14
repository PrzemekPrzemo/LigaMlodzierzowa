<?php
use App\Core\View;
/** @var string $content */
/** @var array $config */
/** @var array $edition */
/** @var array $partners */
$e = fn($v) => View::e($v);
$baseTitle = $title ?? ($edition['title'] ?? 'Liga Młodzieżowa PZSS 2026');
?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= $e($baseTitle) ?> — <?= $e($config['app']['short_name']) ?></title>
    <meta name="description" content="<?= $e($edition['subtitle'] ?? 'Liga Młodzieżowa PZSS 2026 — aktualne wyniki, terminarz, regulamin.') ?>">
    <meta property="og:title"       content="<?= $e($baseTitle) ?>">
    <meta property="og:description" content="<?= $e($edition['subtitle'] ?? '') ?>">
    <meta property="og:type"        content="website">
    <meta name="theme-color" content="#b00020">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;800;900&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=1">
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
</head>
<body>
<?= View::renderRaw('partials/header', ['edition' => $edition]) ?>
<main id="main"><?= $content ?></main>
<?= View::renderRaw('partials/footer', ['edition' => $edition, 'partners' => $partners, 'config' => $config]) ?>
<script src="/assets/js/app.js" defer></script>
</body>
</html>
