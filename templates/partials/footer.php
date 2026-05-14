<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<?php if (!empty($partners)): ?>
<section class="partners">
    <div class="container">
        <h2 class="section-title">Organizator i partnerzy</h2>
        <ul class="partners-grid">
            <?php foreach ($partners as $p): ?>
                <li class="partner">
                    <a href="<?= $e($p['url'] ?: '#') ?>" target="_blank" rel="noopener" title="<?= $e($p['name']) ?>">
                        <?php if (!empty($p['logo'])): ?>
                            <img src="<?= $e($p['logo']) ?>" alt="<?= $e($p['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="partner-text"><?= $e($p['name']) ?></span>
                        <?php endif; ?>
                    </a>
                    <span class="partner-role"><?= $e($p['role']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
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
            </ul>
        </div>
        <div>
            <h3>Kontakt</h3>
            <p>Dział Szkolenia PZSS<br>
                <a href="mailto:szkolenie@pzss.org.pl">szkolenie@pzss.org.pl</a></p>
            <p><a href="<?= $e($config['external']['pzss_home']) ?>" target="_blank" rel="noopener"><?= $e($config['external']['pzss_home']) ?></a></p>
        </div>
    </div>
    <div class="container copy">
        <p>© <?= date('Y') ?> · Liga Młodzieżowa PZSS · Strona nieoficjalna prezentująca dane publikowane przez PZSS.</p>
    </div>
</footer>
