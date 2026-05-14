<?php use App\Core\View; $e = fn($v) => View::e($v); ?>
<section class="page-head"><div class="container">
    <h1>Galeria</h1>
    <p>Zdjęcia z rund eliminacyjnych i Finału.</p>
</div></section>
<section class="container section">
    <?php if (empty($gallery)): ?>
        <div class="alert">Galeria jest jeszcze pusta — zdjęcia pojawią się po pierwszych zawodach.</div>
    <?php else: ?>
        <div class="gallery">
            <?php foreach ($gallery as $g): ?>
                <a class="gal-item" href="<?= $e($g['image_url']) ?>" target="_blank" rel="noopener">
                    <img src="<?= $e($g['thumb_url'] ?: $g['image_url']) ?>" alt="<?= $e($g['title']) ?>" loading="lazy">
                    <?php if (!empty($g['title'])): ?><span class="gal-caption"><?= $e($g['title']) ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
