<?php use App\Core\View; $e = fn($v) => View::e($v); ?>
<section class="page-head"><div class="container">
    <h1>Archiwum sezonów</h1>
    <p>Wyniki ostateczne poprzednich edycji Ligi Młodzieżowej PZSS.</p>
</div></section>
<section class="container section">
    <?php if (empty($editions)): ?>
        <div class="alert">Brak archiwalnych edycji.</div>
    <?php else: ?>
        <div class="clubs-grid">
            <?php foreach ($editions as $ed): ?>
                <a class="club-card" href="/archiwum/<?= (int)$ed['year'] ?>">
                    <h3><?= $e($ed['title']) ?></h3>
                    <p class="muted small"><?= $e($ed['subtitle']) ?></p>
                    <?php if (!empty($ed['state_as_of'])): ?>
                        <p class="muted small">Stan na <?= $e(date('d.m.Y', strtotime($ed['state_as_of']))) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
