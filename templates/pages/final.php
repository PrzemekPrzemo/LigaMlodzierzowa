<?php
use App\Core\View;
$e = fn($v) => View::e($v);
$program = $program ?? [];
?>
<section class="hero hero-final">
    <div class="hero-bg"></div>
    <div class="container hero-inner">
        <span class="hero-eyebrow">Finał Ligi Młodzieżowej PZSS 2026</span>
        <h1 class="hero-title">Finał &amp; Strzelecki Puchar Gdyni</h1>
        <p class="hero-sub">Trzydniowe zawody łączące Finał Ligi Młodzieżowej PZSS ze Strzeleckim Pucharem Gdyni — karabin i pistolet pneumatyczny, 8 zespołów w każdej z konkurencji.</p>
        <?php if ($final && !empty($final['starts_on'])): ?>
            <div class="hero-next">
                <span class="dot"></span>
                <strong><?= $e(date('d.m.Y', strtotime($final['starts_on']))) ?><?= !empty($final['ends_on']) ? ' – ' . $e(date('d.m.Y', strtotime($final['ends_on']))) : '' ?></strong>
                · <?= $e($final['city'] ?? 'Gdynia') ?>
            </div>
        <?php endif; ?>
        <div class="hero-cta mt-1">
            <a class="btn btn-primary" href="#program">Program zawodów</a>
            <a class="btn btn-ghost" href="/spg">O Strzeleckim Pucharze Gdyni</a>
        </div>
    </div>
</section>

<section class="container section grid-2">
    <article class="prose">
        <h2>Format zawodów</h2>
        <p>Finał Ligi Młodzieżowej PZSS rozgrywany jest w formacie 3-dniowym:</p>
        <ul>
            <li><strong>Dzień 1 i 2 — mecze kwalifikacyjne</strong> (system „każdy z każdym" w dwóch grupach: G1: 1, 4, 5, 8 oraz G2: 2, 3, 6, 7).</li>
            <li><strong>Dzień 3 — mecze medalowe</strong> (mecz o złoto/srebro oraz o brąz).</li>
        </ul>

        <h2>Zasady pojedynków</h2>
        <ul>
            <li>Każdy zespół wystawia 3 zawodników z numerami 1–3 (ustalanymi przez trenera; komisja RTS może wskazać korektę).</li>
            <li><strong>Mecze kwalifikacyjne:</strong> 12 min/seria 10 strzałów, punkty 2/1/0 za serię. Karabin — dziesiętne, pistolet — całkowite.</li>
            <li><strong>Mecze finałowe:</strong> 20 strzałów po 50 s, punkty 2/1/0 za strzał, ocena dziesiętna. Remisy → dodatkowy strzał.</li>
        </ul>

        <h2>Nagrody</h2>
        <ul>
            <li>Miejsca I–III: puchary i dyplomy dla klubów.</li>
            <li>Miejsca I–III: medale dla zawodników i trenerów.</li>
            <li>Miejsca I–IV: dyplomy i nagrody rzeczowe dla zespołów.</li>
        </ul>
    </article>

    <aside class="info-box">
        <?php if ($venue): ?>
            <h3>Miejsce zawodów</h3>
            <p><strong><?= $e($venue['name']) ?></strong><br>
                <?= $e($venue['address']) ?>, <?= $e($venue['city']) ?></p>
            <?php if (!empty($venue['map_url'])): ?>
                <a class="btn btn-ghost-d btn-sm" target="_blank" rel="noopener" href="<?= $e($venue['map_url']) ?>">Pokaż na mapie ↗</a>
            <?php endif; ?>
            <p class="muted small mt-1"><?= $e($venue['description']) ?></p>
        <?php endif; ?>
        <h3 class="mt-1">Zgłoszenia</h3>
        <p>Po publikacji ostatecznych rezultatów eliminacji:<br>
            <a href="mailto:szkolenie@pzss.org.pl">szkolenie@pzss.org.pl</a></p>
        <a class="btn btn-primary mt-1" target="_blank" rel="noopener" href="<?= $e($edition['regulation_pdf']) ?>">Regulamin Finału (PDF)</a>
    </aside>
</section>

<section class="container section">
    <h2>Drabinka — schemat grupowy</h2>
    <div class="bracket">
        <div class="group">
            <h3>Grupa G1</h3>
            <ol class="seeds">
                <li><span class="seed-pos">1</span> 1. miejsce po eliminacjach</li>
                <li><span class="seed-pos">4</span> 4. miejsce</li>
                <li><span class="seed-pos">5</span> 5. miejsce</li>
                <li><span class="seed-pos">8</span> 8. miejsce</li>
            </ol>
        </div>
        <div class="group">
            <h3>Grupa G2</h3>
            <ol class="seeds">
                <li><span class="seed-pos">2</span> 2. miejsce</li>
                <li><span class="seed-pos">3</span> 3. miejsce</li>
                <li><span class="seed-pos">6</span> 6. miejsce</li>
                <li><span class="seed-pos">7</span> 7. miejsce</li>
            </ol>
        </div>
        <div class="group medals">
            <h3>Mecze medalowe</h3>
            <p>1. z G1 vs 1. z G2 → <strong>złoto</strong></p>
            <p>2. z G1 vs 2. z G2 → <strong>brąz</strong></p>
        </div>
    </div>
</section>

<?php if (!empty($program)): ?>
<section id="program" class="container section">
    <header class="section-head">
        <h2>Program zawodów</h2>
        <p class="muted">Ramowy plan trzech dni Finału</p>
    </header>
    <div class="program">
        <?php foreach ($program as $dayNo => $day): ?>
            <article class="program-day">
                <h3><?= $e($day['label']) ?></h3>
                <ol class="program-list">
                    <?php foreach ($day['rows'] as $ev): ?>
                        <li class="ev ev-<?= $e($ev['kind']) ?>">
                            <span class="ev-time">
                                <?= $e($ev['time_start'] ? substr($ev['time_start'],0,5) : '') ?>
                                <?php if (!empty($ev['time_end'])): ?>–<?= $e(substr($ev['time_end'],0,5)) ?><?php endif; ?>
                            </span>
                            <span class="ev-body">
                                <strong><?= $e($ev['title']) ?></strong>
                                <?php if (!empty($ev['location'])): ?><span class="muted small"> · <?= $e($ev['location']) ?></span><?php endif; ?>
                            </span>
                            <span class="ev-kind tag tag-<?= $e($ev['kind']) ?>"><?= $e($ev['kind']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="muted small">Program ramowy — godziny mogą ulec zmianie. Wiążący jest komunikat organizacyjny.</p>
</section>
<?php endif; ?>

<?php if ($venue && !empty($venue['lat']) && !empty($venue['lng'])): ?>
<section class="container section">
    <h2>Mapa</h2>
    <iframe
        title="Mapa strzelnicy"
        loading="lazy"
        class="map-frame"
        src="https://www.openstreetmap.org/export/embed.html?bbox=<?= ($venue['lng']-0.01) ?>,<?= ($venue['lat']-0.01) ?>,<?= ($venue['lng']+0.01) ?>,<?= ($venue['lat']+0.01) ?>&amp;marker=<?= $venue['lat'] ?>,<?= $venue['lng'] ?>"></iframe>
</section>
<?php endif; ?>
