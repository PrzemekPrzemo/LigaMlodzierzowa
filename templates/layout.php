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
<div class="unofficial-bar" role="note">
    <div class="container unofficial-bar-inner">
        <span class="unofficial-badge">Strona NIEOFICJALNA</span>
        <span class="unofficial-text">Serwis fanowski — dane prezentowane są na podstawie publikacji <a href="<?= $e($config['external']['pzss_home']) ?>" target="_blank" rel="noopener">PZSS</a>. Oficjalna strona Ligi: <a href="<?= $e($edition['pzss_url']) ?>" target="_blank" rel="noopener">pzss.org.pl</a>.</span>
    </div>
</div>
<?= View::renderRaw('partials/header', ['edition' => $edition]) ?>
<main id="main"><?= $content ?></main>
<?= View::renderRaw('partials/sponsors_marquee', ['marqueeSponsors' => $marqueeSponsors ?? []]) ?>
<?= View::renderRaw('partials/footer', ['edition' => $edition, 'partners' => $partners, 'config' => $config]) ?>

<div id="welcome-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="welcome-title" hidden>
    <div class="modal-backdrop" data-close></div>
    <div class="modal-card">
        <button class="modal-close" type="button" data-close aria-label="Zamknij">×</button>
        <div class="modal-emoji" aria-hidden="true">🚧</div>
        <h2 id="welcome-title">Strona dalej w przygotowaniu</h2>
        <p>Chcesz pomóc w jej rozwoju? Napisz na:</p>
        <p><a class="modal-mail" href="mailto:przemek@szulecki.pl?subject=Liga%20M%C5%82odzie%C5%BCowa%20–%20pomoc">przemek@szulecki.pl</a></p>
        <div class="modal-actions">
            <button class="btn btn-primary" type="button" data-close>OK, rozumiem</button>
        </div>
    </div>
</div>

<script src="/assets/js/app.js" defer></script>
</body>
</html>
