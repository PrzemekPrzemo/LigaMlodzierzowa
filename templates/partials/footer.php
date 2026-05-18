<?php
use App\Core\View;
use App\Repository\MediaRepository;
$e = fn($v) => View::e($v);

// Footer "Organizator i partnerzy" — bierze TOP partnerów z tabeli sponsors
// (zarządzane w /admin/sponsorzy). Pokazuje patronat + sponsora głównego + partnerów.
$footerHighlights = $partners ?? [];
$cats = MediaRepository::CATEGORIES;
?>
<?php if (!empty($footerHighlights)): ?>
<section class="partners">
    <div class="container">
        <h2 class="section-title">Organizator i partnerzy</h2>
        <ul class="partners-grid">
            <?php foreach ($footerHighlights as $p):
                $href = $p['url'] ?? null;
                $catKey = $p['category'] ?? 'partner';
                $catLabel = $cats[$catKey][0] ?? 'Partner';
            ?>
                <li class="partner">
                    <?php if ($href): ?><a href="<?= $e($href) ?>" target="_blank" rel="noopener" title="<?= $e($p['name']) ?>"><?php endif; ?>
                        <?php if (!empty($p['logo'])): ?>
                            <img src="<?= $e($p['logo']) ?>" alt="<?= $e($p['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="partner-text"><?= $e($p['name']) ?></span>
                        <?php endif; ?>
                    <?php if ($href): ?></a><?php endif; ?>
                    <span class="partner-role"><?= $e($catLabel) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="muted small text-center"><a href="/partnerzy">Wszyscy patroni, sponsorzy i partnerzy →</a></p>
    </div>
</section>
<?php endif; ?>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <h3>Liga Młodzieżowa PZSS <?= (int)$edition['year'] ?></h3>
            <p><?= $e($edition['organizer']) ?></p>
            <p>Cykl zawodów zespołowych do lat 17 (rocznik 2007 i młodsi) — karabin i pistolet pneumatyczny.</p>
        </div>
        <div>
            <h3>Dokumenty PZSS</h3>
            <ul class="list-clean">
                <li><a target="_blank" rel="noopener" href="<?= $e($edition['pzss_url']) ?>">Strona Ligi w serwisie PZSS</a></li>
                <li><a target="_blank" rel="noopener" href="<?= $e($edition['regulation_pdf']) ?>">Regulamin Finału 2026 (PDF)</a></li>
                <li><a target="_blank" rel="noopener" href="<?= $e($edition['results_pdf']) ?>">Aktualne wyniki (PDF)</a></li>
                <li><a href="/partnerzy">Patroni, sponsorzy, partnerzy</a></li>
            </ul>
        </div>
        <div>
            <h3>Kontakt</h3>
            <p>Dział Szkolenia PZSS<br>
                <a href="mailto:szkolenie@pzss.org.pl">szkolenie@pzss.org.pl</a></p>
            <p>Pomoc przy stronie:<br><a href="mailto:przemek@szulecki.pl">przemek@szulecki.pl</a></p>
        </div>
    </div>
    <div class="container copy">
        <p>© <?= date('Y') ?> · Liga Młodzieżowa PZSS · Strona NIEOFICJALNA prezentująca dane publikowane przez PZSS.</p>
    </div>
</footer>
