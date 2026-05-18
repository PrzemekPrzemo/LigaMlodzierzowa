<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head">
    <div class="container">
        <h1>Partnerzy i sponsorzy</h1>
        <p>Patroni honorowi, sponsorzy i partnerzy medialni Ligi Młodzieżowej PZSS oraz Strzeleckiego Pucharu Gdyni.</p>
    </div>
</section>

<section class="container section">
    <?php if (empty($groups)): ?>
        <div class="alert">Lista partnerów jest jeszcze pusta. Wkrótce uzupełnimy.</div>
    <?php endif; ?>

    <?php foreach ($groups as $cat => $g): ?>
        <article class="partner-section <?= $e($g['cls']) ?>">
            <h2><?= $e($g['label']) ?> <span class="muted small">(<?= count($g['items']) ?>)</span></h2>
            <ul class="sponsors-grid">
                <?php foreach ($g['items'] as $sp): ?>
                    <li class="sponsor sponsor-<?= $e($sp['tier']) ?>">
                        <?php $href = $sp['url'] ?: $sp['instagram_url'] ?: $sp['facebook_url']; ?>
                        <?php if ($href): ?><a href="<?= $e($href) ?>" target="_blank" rel="noopener" title="<?= $e($sp['name']) ?>"><?php endif; ?>
                            <?php if (!empty($sp['logo'])): ?>
                                <img src="<?= $e($sp['logo']) ?>" alt="<?= $e($sp['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <span class="sponsor-text"><?= $e($sp['name']) ?></span>
                            <?php endif; ?>
                        <?php if ($href): ?></a><?php endif; ?>
                        <strong class="sponsor-name"><?= $e($sp['name']) ?></strong>
                        <?php if (!empty($sp['description'])): ?>
                            <p class="muted small"><?= $e($sp['description']) ?></p>
                        <?php endif; ?>
                        <div class="sponsor-links">
                            <?php if (!empty($sp['url'])): ?>
                                <a href="<?= $e($sp['url']) ?>" target="_blank" rel="noopener" title="Strona WWW" aria-label="Strona WWW <?= $e($sp['name']) ?>">🌐</a>
                            <?php endif; ?>
                            <?php if (!empty($sp['instagram_url'])): ?>
                                <a href="<?= $e($sp['instagram_url']) ?>" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram">📷</a>
                            <?php endif; ?>
                            <?php if (!empty($sp['facebook_url'])): ?>
                                <a href="<?= $e($sp['facebook_url']) ?>" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook">f</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>
    <?php endforeach; ?>

    <p class="muted small mt-2">
        Chcesz wesprzeć Ligę lub Puchar Gdyni jako sponsor lub partner medialny?
        Napisz: <a href="mailto:przemek@szulecki.pl?subject=Wsp%C3%B3%C5%82praca%20Liga%20M%C5%82odzie%C5%BCowa">przemek@szulecki.pl</a>.
    </p>
</section>
