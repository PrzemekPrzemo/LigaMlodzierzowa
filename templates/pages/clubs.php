<?php use App\Core\View; $e = fn($v) => View::e($v);
$fmt = fn($v) => $v === null ? '—' : (fmod((float)$v,1.0)===0.0
    ? (string)(int)$v : str_replace('.', ',', rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.'))); ?>
<section class="page-head"><div class="container">
    <h1>Kluby — <?= $e($edition['title']) ?></h1>
    <p>Wszystkie kluby uczestniczące w bieżącej edycji Ligi.</p>
</div></section>

<section class="container section">
    <?php if (empty($clubs)): ?>
        <div class="alert">Brak klubów. Zaimportuj wyniki rund.</div>
    <?php else: ?>
        <div class="clubs-grid">
            <?php foreach ($clubs as $c): ?>
                <a class="club-card" href="/klub/<?= $e($c['slug']) ?>">
                    <h3><?= $e($c['name']) ?></h3>
                    <p class="muted small"><?= $e($c['city']) ?></p>
                    <dl class="club-kpi">
                        <div><dt>Zespołów</dt><dd><?= (int)$c['teams_count'] ?></dd></div>
                        <div><dt>Najlepszy wynik</dt><dd><?= $e($fmt($c['best_score'])) ?> <span class="muted small"><?= $e($c['best_discipline']) ?></span></dd></div>
                    </dl>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
