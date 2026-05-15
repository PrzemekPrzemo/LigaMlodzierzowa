<?php use App\Core\View; $e = fn($v) => View::e($v); ?>
<section class="page-head"><div class="container">
    <h1>Transmisje na żywo</h1>
    <p>Mecze grupowe i medalowe — bezpośrednia transmisja online.</p>
</div></section>
<section class="container section">
    <?php if (empty($lives)): ?>
        <div class="alert">Brak zaplanowanych transmisji.</div>
    <?php else: ?>
        <div class="live-grid">
            <?php foreach ($lives as $lv): ?>
                <article class="live-card live-<?= $e($lv['status']) ?>">
                    <div class="live-embed">
                        <iframe src="<?= $e($lv['embed_url']) ?>" title="<?= $e($lv['title']) ?>" allowfullscreen loading="lazy"></iframe>
                    </div>
                    <div class="live-meta">
                        <span class="tag tag-<?= $e($lv['status']) ?>"><?= $e($lv['status']) ?></span>
                        <strong><?= $e($lv['title']) ?></strong>
                        <?php if (!empty($lv['starts_at'])): ?>
                            <span class="muted small"><?= $e(date('d.m.Y H:i', strtotime($lv['starts_at']))) ?></span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
