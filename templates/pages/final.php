<?php
use App\Core\View;
$e = fn($v) => View::e($v);
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
                Termin: <strong><?= $e(date('d.m.Y', strtotime($final['starts_on']))) ?><?= !empty($final['ends_on']) ? ' – ' . $e(date('d.m.Y', strtotime($final['ends_on']))) : '' ?></strong>
                · <?= $e($final['city'] ?? 'Gdynia') ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="container section grid-2">
    <article class="prose">
        <h2>Format zawodów</h2>
        <p>Finał Ligi Młodzieżowej PZSS rozgrywany jest w formacie 3-dniowym:</p>
        <ul>
            <li><strong>Dzień 1 i 2 — mecze kwalifikacyjne</strong> (system „każdy z każdym” w dwóch grupach: G1: 1, 4, 5, 8 oraz G2: 2, 3, 6, 7).</li>
            <li><strong>Dzień 3 — mecze medalowe</strong> (mecz o złoto/srebro oraz o brąz).</li>
        </ul>

        <h2>Zasady pojedynków</h2>
        <ul>
            <li>Każdy zespół wystawia 3 zawodników z numerami 1–3 (ustalanymi przez trenera; komisja RTS może wskazać korektę).</li>
            <li><strong>Mecze kwalifikacyjne:</strong> 12 min/seria 10 strzałów, punkty 2/1/0 za serię. Karabin — ocena dziesiętna, pistolet — całkowita.</li>
            <li><strong>Mecze finałowe:</strong> 20 strzałów po 50 s, punkty 2/1/0 za strzał, ocena dziesiętna. Remisy rozstrzyga dodatkowy strzał.</li>
            <li>Strzelnica: min. 26 stanowisk z tarczami elektronicznymi, ekrany wyników, nagłośnienie.</li>
        </ul>

        <h2>Nagrody</h2>
        <ul>
            <li>Miejsca I–III: puchary i dyplomy dla klubów.</li>
            <li>Miejsca I–III: medale dla zawodników i trenerów.</li>
            <li>Miejsca I–IV: dyplomy i nagrody rzeczowe dla zespołów.</li>
        </ul>
    </article>

    <aside class="info-box">
        <h3>Strzelecki Puchar Gdyni</h3>
        <p>W edycji 2026 Finał Ligi Młodzieżowej PZSS jest organizowany wspólnie ze Strzeleckim Pucharem Gdyni — co podnosi rangę zawodów i pozwala młodym zawodnikom rywalizować w prestiżowej imprezie.</p>
        <h3>Zgłoszenia</h3>
        <p>Po publikacji ostatecznych rezultatów eliminacji kluby potwierdzają udział pod adresem:<br>
            <a href="mailto:szkolenie@pzss.org.pl">szkolenie@pzss.org.pl</a>.</p>
        <p>Koszty zakwaterowania i wyżywienia: rezerwowane przez organizatora; informacja indywidualnie do klubów.</p>
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
                <li><span class="seed-pos">2</span> 2. miejsce po eliminacjach</li>
                <li><span class="seed-pos">3</span> 3. miejsce</li>
                <li><span class="seed-pos">6</span> 6. miejsce</li>
                <li><span class="seed-pos">7</span> 7. miejsce</li>
            </ol>
        </div>
        <div class="group medals">
            <h3>Mecze medalowe</h3>
            <p>1. z G1 vs 1. z G2 → mecz o <strong>złoto</strong></p>
            <p>2. z G1 vs 2. z G2 → mecz o <strong>brąz</strong></p>
        </div>
    </div>
</section>
