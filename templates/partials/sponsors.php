<?php use App\Core\View; $e = fn($v) => View::e($v); $sponsors = $sponsors ?? []; if (empty($sponsors)) return; ?>
<section class="container section">
    <header class="section-head">
        <h2><?= isset($title_sponsors) ? $e($title_sponsors) : 'Partnerzy i sponsorzy' ?></h2>
    </header>
    <ul class="sponsors-grid">
        <?php foreach ($sponsors as $sp): ?>
            <li class="sponsor sponsor-<?= $e($sp['tier']) ?>">
                <a href="<?= $e($sp['url'] ?: '#') ?>" target="_blank" rel="noopener">
                    <?php if (!empty($sp['logo'])): ?>
                        <img src="<?= $e($sp['logo']) ?>" alt="<?= $e($sp['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <span class="sponsor-text"><?= $e($sp['name']) ?></span>
                    <?php endif; ?>
                </a>
                <span class="sponsor-tier tier-<?= $e($sp['tier']) ?>"><?= $e($sp['tier']) ?></span>
                <?php if (!empty($sp['description'])): ?>
                    <p class="muted small"><?= $e($sp['description']) ?></p>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
