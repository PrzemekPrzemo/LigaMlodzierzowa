<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head"><div class="container"><h1>Aktualności</h1></div></section>
<section class="container section">
    <?php if (empty($news)): ?>
        <div class="alert">Brak wpisów.</div>
    <?php else: ?>
        <div class="news-grid">
            <?php foreach ($news as $n): ?>
                <article class="news-card <?= !empty($n['is_pinned']) ? 'pinned' : '' ?>">
                    <time><?= $e(date('d.m.Y', strtotime($n['published_at']))) ?></time>
                    <h2><a href="/aktualnosci/<?= $e($n['slug']) ?>"><?= $e($n['title']) ?></a></h2>
                    <p><?= $e($n['lead']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
