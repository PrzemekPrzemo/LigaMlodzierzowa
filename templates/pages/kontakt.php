<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head"><div class="container"><h1>Kontakt</h1></div></section>
<section class="container section grid-2">
    <article class="prose">
        <h2>Polski Związek Strzelectwa Sportowego — Dział Szkolenia</h2>
        <p><a href="mailto:szkolenie@pzss.org.pl">szkolenie@pzss.org.pl</a></p>
        <p><a target="_blank" rel="noopener" href="<?= $e($config['external']['pzss_home']) ?>"><?= $e($config['external']['pzss_home']) ?></a></p>
        <h2>Strzelecki Puchar Gdyni / Finał 2026</h2>
        <p>Organizator zawodów w Gdyni — informacje o programie i zakwaterowaniu zostaną opublikowane wraz z komunikatem organizacyjnym po zakończeniu eliminacji.</p>
    </article>
    <aside class="info-box">
        <h3>Materiały PZSS</h3>
        <ul class="list-clean">
            <li><a target="_blank" rel="noopener" href="<?= $e($edition['pzss_url']) ?>">Liga Młodzieżowa 2026 — strona PZSS</a></li>
            <li><a target="_blank" rel="noopener" href="<?= $e($edition['results_pdf']) ?>">Aktualne wyniki (PDF)</a></li>
            <li><a target="_blank" rel="noopener" href="<?= $e($edition['regulation_pdf']) ?>">Regulamin Finału (PDF)</a></li>
        </ul>
    </aside>
</section>
