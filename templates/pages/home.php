<?php
use App\Core\View;
$e = fn($v) => View::e($v);
$stateAsOf = $edition['state_as_of'] ?? null;
?>
<section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-inner">
        <span class="hero-eyebrow"><?= $e($edition['organizer']) ?> · do lat 17</span>
        <h1 class="hero-title"><?= $e($edition['title']) ?></h1>
        <p class="hero-sub"><?= $e($edition['subtitle']) ?></p>
        <div class="hero-cta">
            <a class="btn btn-primary" href="/wyniki">Aktualne wyniki</a>
            <a class="btn btn-ghost"   href="/regulamin">Regulamin</a>
            <a class="btn btn-ghost"   href="/final-puchar-gdyni">Finał &amp; Puchar Gdyni</a>
        </div>
        <?php if (!empty($next)): ?>
            <div class="hero-next">
                <span class="dot"></span>
                Najbliższa runda: <strong><?= $e($next['short_label']) ?></strong>
                <?php if (!empty($next['starts_on'])): ?>
                    · <?= $e(date('d.m.Y', strtotime($next['starts_on']))) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="container section">
    <header class="section-head">
        <h2>O Lidze Młodzieżowej</h2>
        <p>Stan na <?= $e($stateAsOf ? date('d.m.Y', strtotime($stateAsOf)) : date('d.m.Y')) ?></p>
    </header>
    <div class="prose">
        <?= nl2br($e($edition['description'])) ?>
    </div>

    <div class="cards mt-2">
        <article class="card">
            <h3>Format</h3>
            <p>Zespół trzyosobowy z jednego klubu (możliwy mieszany). Każdy klub może wystawić 1 zespół w każdej z dyscyplin.</p>
        </article>
        <article class="card">
            <h3>Konkurencje</h3>
            <p><strong>Karabin pneumatyczny</strong> oraz <strong>pistolet pneumatyczny</strong>. Brane są pod uwagę pierwsze 4 serie strzeleckie.</p>
        </article>
        <article class="card">
            <h3>Awans do Finału</h3>
            <p>Do Finału kwalifikuje się <strong>8 najlepszych zespołów</strong> w każdej z konkurencji wg <em>najwyższego wyniku</em> z 4 rund eliminacyjnych.</p>
        </article>
        <article class="card hl">
            <h3>Finał 2026</h3>
            <p>Połączony ze <strong>Strzeleckim Pucharem Gdyni</strong> — 3 dni: mecze grupowe i medalowe.</p>
        </article>
    </div>
</section>

<?php if (!empty($rounds)): ?>
<section class="container section">
    <header class="section-head">
        <h2>Terminarz sezonu</h2>
        <a class="link-more" href="/terminarz">Pełny terminarz →</a>
    </header>
    <ol class="timeline">
        <?php foreach ($rounds as $r): ?>
            <li class="timeline-item <?= $r['is_final'] ? 'is-final' : '' ?> status-<?= $e($r['status']) ?>">
                <div class="t-num"><?= $r['is_final'] ? 'FINAŁ' : 'R' . (int)$r['number'] ?></div>
                <div class="t-body">
                    <h3><?= $e($r['short_label']) ?></h3>
                    <p>
                        <?php if (!empty($r['city'])): ?><?= $e($r['city']) ?><?php endif; ?>
                        <?php if (!empty($r['starts_on'])): ?> · <?= $e(date('d.m.Y', strtotime($r['starts_on']))) ?><?php endif; ?>
                        <?php if (!empty($r['ends_on']) && $r['ends_on'] !== $r['starts_on']): ?> – <?= $e(date('d.m.Y', strtotime($r['ends_on']))) ?><?php endif; ?>
                    </p>
                    <span class="tag tag-<?= $e($r['status']) ?>"><?= $e($r['status'] === 'finished' ? 'rozegrana' : ($r['status'] === 'ongoing' ? 'w trakcie' : 'zaplanowana')) ?></span>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
<?php endif; ?>

<?php if (!empty($news)): ?>
<section class="container section">
    <header class="section-head">
        <h2>Aktualności</h2>
        <a class="link-more" href="/aktualnosci">Wszystkie aktualności →</a>
    </header>
    <div class="news-grid">
        <?php foreach ($news as $n): ?>
            <article class="news-card">
                <time><?= $e(date('d.m.Y', strtotime($n['published_at']))) ?></time>
                <h3><a href="/aktualnosci/<?= $e($n['slug']) ?>"><?= $e($n['title']) ?></a></h3>
                <p><?= $e($n['lead']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($docs)): ?>
<section class="container section">
    <header class="section-head">
        <h2>Dokumenty PZSS</h2>
    </header>
    <ul class="doc-list">
        <?php foreach ($docs as $d): ?>
            <li class="doc doc-<?= $e($d['kind']) ?>">
                <a href="<?= $e($d['url']) ?>" target="_blank" rel="noopener">
                    <span class="doc-kind"><?= $e($d['kind']) ?></span>
                    <span class="doc-title"><?= $e($d['title']) ?></span>
                    <span class="doc-src"><?= $e($d['source']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>
