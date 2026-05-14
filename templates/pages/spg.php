<?php use App\Core\View; $e = fn($v) => View::e($v); ?>
<section class="hero hero-spg">
    <div class="hero-bg"></div>
    <div class="container hero-inner">
        <span class="hero-eyebrow">Strzelectwo · Gdynia</span>
        <h1 class="hero-title">Strzelecki Puchar Gdyni</h1>
        <p class="hero-sub">Cykl zawodów strzeleckich rozgrywanych w Gdyni. W 2026 roku gospodarz <strong>Finału Ligi Młodzieżowej PZSS</strong> — wspólna impreza dla młodzieży i dorosłych zawodników.</p>
        <div class="hero-cta">
            <a class="btn btn-primary" href="/final-puchar-gdyni">Program Finału 2026</a>
            <a class="btn btn-ghost" href="#info">O Pucharze</a>
        </div>
    </div>
</section>

<section id="info" class="container section grid-2">
    <article class="prose">
        <h2>O zawodach</h2>
        <p>Strzelecki Puchar Gdyni to coroczne zawody pneumatyczne organizowane w Gdyni. Łączą rywalizację sportową z promocją strzelectwa wśród dzieci i młodzieży, oferując program zawodów oparty o profesjonalne tarcze elektroniczne i pełną oprawę medialną.</p>
        <h2>2026 — Puchar Gdyni × Liga Młodzieżowa PZSS</h2>
        <p>W edycji 2026 Puchar Gdyni jest oficjalnym gospodarzem <a href="/final-puchar-gdyni">Finału Ligi Młodzieżowej PZSS</a>. Trzy dni rywalizacji obejmują mecze grupowe (G1/G2) i mecze medalowe — szczegóły w programie.</p>
        <h2>Dlaczego warto przyjechać?</h2>
        <ul>
            <li>26+ stanowisk z tarczami elektronicznymi, ekrany prezentacji wyników</li>
            <li>Oprawa medialna: komentarz, nagłośnienie, transmisja meczów finałowych</li>
            <li>Atrakcyjna lokalizacja w nadmorskiej Gdyni</li>
            <li>Rywalizacja na poziomie ogólnopolskim, kwalifikacje do Finału Ligi Europejskiej ESC</li>
        </ul>
    </article>
    <aside class="info-box spg">
        <img src="/assets/img/logo-puchar-gdyni.svg" alt="Strzelecki Puchar Gdyni" class="info-logo">
        <?php if ($venue): ?>
            <h3>Strzelnica</h3>
            <p><strong><?= $e($venue['name']) ?></strong><br>
                <?= $e($venue['address']) ?>, <?= $e($venue['city']) ?></p>
            <?php if (!empty($venue['map_url'])): ?>
                <a class="btn btn-ghost-d btn-sm" target="_blank" rel="noopener" href="<?= $e($venue['map_url']) ?>">Pokaż na mapie ↗</a>
            <?php endif; ?>
        <?php endif; ?>
        <h3 class="mt-1">Kontakt organizator</h3>
        <p>Strona w przygotowaniu — pełne dane organizacyjne udostępnimy wraz z komunikatem.</p>
    </aside>
</section>

<section class="container section">
    <header class="section-head"><h2>Edycja 2026 — co warto wiedzieć</h2></header>
    <div class="cards">
        <article class="card spg-card">
            <h3>Format</h3>
            <p>3 dni rywalizacji: 2 dni mecze grupowe + 1 dzień mecze medalowe. Karabin i pistolet pneumatyczny.</p>
        </article>
        <article class="card spg-card">
            <h3>Uczestnicy</h3>
            <p>8 najlepszych zespołów z eliminacji Ligi Młodzieżowej PZSS w każdej z konkurencji + zawodnicy Pucharu Gdyni.</p>
        </article>
        <article class="card spg-card">
            <h3>Klasyfikacja</h3>
            <p>Punkty 2/1/0 za serię (kwalifikacje) lub strzał (mecze medalowe). Remisy rozstrzyga dodatkowy strzał.</p>
        </article>
        <article class="card spg-card hl">
            <h3>Nagrody</h3>
            <p>Puchary, medale, nagrody rzeczowe dla zespołów I–IV oraz dyplomy dla klubów i trenerów.</p>
        </article>
    </div>
</section>
