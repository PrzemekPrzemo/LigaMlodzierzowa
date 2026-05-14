<?php use App\Core\View; $e = fn($v) => View::e($v); ?>
<h1>Pulpit</h1>
<p class="muted">Edycja: <strong><?= $e($edition['title']) ?></strong></p>

<div class="kpi-grid">
    <div class="kpi"><span class="kpi-num"><?= (int)$stats['teams_total'] ?></span><span class="kpi-lab">zespołów</span></div>
    <div class="kpi"><span class="kpi-num"><?= (int)$stats['scores_total'] ?></span><span class="kpi-lab">wyników</span></div>
    <div class="kpi"><span class="kpi-num"><?= (int)$stats['rounds_done'] ?>/4</span><span class="kpi-lab">rund rozegranych</span></div>
    <div class="kpi"><span class="kpi-num"><?= (int)$stats['news_total'] ?></span><span class="kpi-lab">aktualności</span></div>
</div>

<div class="admin-grid mt-2">
    <a class="admin-tile" href="/admin/news"><h3>Aktualności</h3><p>Dodaj/edytuj wpisy, oznacz przypięte.</p></a>
    <a class="admin-tile" href="/admin/rundy"><h3>Rundy &amp; wyniki</h3><p>Wpisz wyniki kolejnych rund, zmień status.</p></a>
    <a class="admin-tile" href="/admin/import"><h3>Import CSV</h3><p>Wgraj wyniki rundy z arkusza.</p></a>
    <a class="admin-tile" href="/" target="_blank" rel="noopener"><h3>Podgląd strony</h3><p>Zobacz publiczną wersję.</p></a>
</div>
