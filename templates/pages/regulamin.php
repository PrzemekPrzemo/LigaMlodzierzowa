<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head">
    <div class="container">
        <h1>Regulamin — <?= $e($edition['title']) ?></h1>
        <p>Streszczenie regulaminu PZSS Ligi Młodzieżowej do lat 17 oraz Finału.</p>
    </div>
</section>

<section class="container section grid-2">
    <article class="prose">
        <h2>Cele Ligi</h2>
        <ul>
            <li>popularyzacja strzelectwa wśród dzieci i młodzieży,</li>
            <li>zachęcenie klubów do udziału w systemie szkolenia,</li>
            <li>podniesienie poziomu sportowego zawodników,</li>
            <li>przygotowanie do rywalizacji w Finale Ligi Europejskiej ESC.</li>
        </ul>

        <h2>Zasady ogólne</h2>
        <ul>
            <li>Uczestnictwo: każdy klub z licencją PZSS.</li>
            <li>Konkurencje: <strong>karabin pneumatyczny</strong>, <strong>pistolet pneumatyczny</strong>.</li>
            <li>Zespół: <strong>3 zawodników</strong> z jednego klubu (możliwy mieszany), kategoria <em>młodzik</em> lub <em>junior młodszy</em>, 40 lub 50 strzałów odpowiednio.</li>
            <li>Wiek: rocznik <strong>2007 i młodsi</strong>.</li>
        </ul>

        <h2>Rundy eliminacyjne</h2>
        <ol>
            <li>Zlot Orlików</li>
            <li>Puchar Bydgoszczy</li>
            <li>Puchar Prezesa PZSS</li>
            <li>Złoty Muszkiet / Złota Krócica</li>
        </ol>
        <p>Klasyfikacja: suma wyników 3 zawodników z pierwszych 4 serii. Do Finału kwalifikuje się <strong>8 najlepszych zespołów</strong> w każdej z konkurencji wg najwyższego wyniku z rund.</p>

        <h2>Finał</h2>
        <ul>
            <li>Format 3-dniowy: 2 dni meczów grupowych + 1 dzień meczów medalowych.</li>
            <li>Grupy: <strong>G1: 1, 4, 5, 8</strong> · <strong>G2: 2, 3, 6, 7</strong>.</li>
            <li>Mecze kwalifikacyjne: 12 min/serię, punkty 2/1/0 za serię (karabin — dziesiętne, pistolet — całkowite).</li>
            <li>Mecze finałowe: 20 strzałów po 50 s, punkty 2/1/0 za strzał, remisy rozstrzyga dodatkowy strzał (dziesiętne).</li>
            <li>Wymagania strzelnicy: min. 26 stanowisk z tarczami elektronicznymi, ekrany wyników, nagłośnienie.</li>
        </ul>

        <h2>Zgłoszenia</h2>
        <p>Potwierdzenie udziału (ilość osób, czas przyjazdu) na: <a href="mailto:szkolenie@pzss.org.pl">szkolenie@pzss.org.pl</a> w terminie wskazanym w komunikacie po publikacji rezultatów eliminacji.</p>
    </article>

    <aside class="docs-aside">
        <h3>Dokumenty źródłowe</h3>
        <ul class="doc-list">
            <li class="doc doc-regulamin">
                <a target="_blank" rel="noopener" href="<?= $e($edition['regulation_pdf']) ?>">
                    <span class="doc-kind">regulamin</span>
                    <span class="doc-title">Regulamin Finału Ligi Młodzieżowej 2026 (PDF)</span>
                    <span class="doc-src">PZSS</span>
                </a>
            </li>
            <li class="doc doc-wyniki">
                <a target="_blank" rel="noopener" href="<?= $e($edition['results_pdf']) ?>">
                    <span class="doc-kind">wyniki</span>
                    <span class="doc-title">Aktualne wyniki Ligi 2026 (PDF)</span>
                    <span class="doc-src">PZSS</span>
                </a>
            </li>
            <li class="doc">
                <a target="_blank" rel="noopener" href="<?= $e($edition['pzss_url']) ?>">
                    <span class="doc-kind">www</span>
                    <span class="doc-title">Strona Ligi w serwisie PZSS</span>
                    <span class="doc-src">PZSS</span>
                </a>
            </li>
        </ul>
        <p class="muted small">Streszczenie ma charakter informacyjny — wiążąca jest treść oficjalnego regulaminu PZSS.</p>
    </aside>
</section>
