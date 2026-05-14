<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head">
    <div class="container">
        <h1>Terminarz — <?= $e($edition['title']) ?></h1>
        <p>4 rundy eliminacyjne oraz Finał Ligi Młodzieżowej połączony ze Strzeleckim Pucharem Gdyni.</p>
    </div>
</section>

<section class="container section">
    <?php if (empty($rounds)): ?>
        <div class="alert">Brak danych o rundach.</div>
    <?php else: ?>
        <ol class="timeline">
            <?php foreach ($rounds as $r): ?>
                <li class="timeline-item <?= $r['is_final'] ? 'is-final' : '' ?> status-<?= $e($r['status']) ?>">
                    <div class="t-num"><?= $r['is_final'] ? 'FINAŁ' : 'R' . (int)$r['number'] ?></div>
                    <div class="t-body">
                        <h2><?= $e($r['label']) ?></h2>
                        <p class="muted">
                            <?php if (!empty($r['city'])): ?><?= $e($r['city']) ?><?php endif; ?>
                            <?php if (!empty($r['venue']) && $r['venue'] !== $r['city']): ?> · <?= $e($r['venue']) ?><?php endif; ?>
                            <?php if (!empty($r['starts_on'])): ?> · <?= $e(date('d.m.Y', strtotime($r['starts_on']))) ?><?php endif; ?>
                            <?php if (!empty($r['ends_on']) && $r['ends_on'] !== $r['starts_on']): ?> – <?= $e(date('d.m.Y', strtotime($r['ends_on']))) ?><?php endif; ?>
                        </p>
                        <span class="tag tag-<?= $e($r['status']) ?>"><?= $e($r['status'] === 'finished' ? 'rozegrana' : ($r['status'] === 'ongoing' ? 'w trakcie' : 'zaplanowana')) ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</section>
