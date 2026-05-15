<?php
use App\Core\View;
use App\Repository\MediaRepository;
$e = fn($v) => View::e($v);
$items = $marqueeSponsors ?? [];
if (empty($items)) return;
$cats = MediaRepository::CATEGORIES;
?>
<aside class="marquee" aria-label="Patroni, sponsorzy i partnerzy">
    <div class="marquee-label">Patroni · Sponsorzy · Partnerzy</div>
    <div class="marquee-viewport">
        <div class="marquee-track">
            <?php for ($pass = 0; $pass < 2; $pass++): /* duplikat dla nieskończonego scrolla */ ?>
                <?php foreach ($items as $sp):
                    $catLabel = $cats[$sp['category']][0] ?? '';
                    $href = $sp['url'] ?: $sp['instagram_url'] ?: $sp['facebook_url'] ?: '#';
                ?>
                    <a class="marquee-item" href="<?= $e($href) ?>" target="_blank" rel="noopener" title="<?= $e($sp['name'] . ' — ' . $catLabel) ?>" <?= $href === '#' ? 'aria-hidden="true" tabindex="-1"' : '' ?>>
                        <?php if (!empty($sp['logo'])): ?>
                            <img src="<?= $e($sp['logo']) ?>" alt="<?= $e($sp['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="marquee-item-text"><?= $e($sp['name']) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>
    <a class="marquee-more" href="/partnerzy">Wszyscy partnerzy →</a>
</aside>
