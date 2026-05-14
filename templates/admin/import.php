<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); ?>
<header class="admin-head">
    <h1>Import wyników (CSV)</h1>
    <a class="btn btn-ghost-d" href="/admin/rundy">← Rundy</a>
</header>

<div class="prose">
    <p>Format pliku CSV (przecinek jako separator, UTF-8, opcjonalny nagłówek):</p>
    <pre>ZESPOŁ,WYNIK
ZAWISZA Bydgoszcz,1243.1
LIDER-AMICUS Lębork,1231.3
GWARDIA Zielona Góra,1230,4</pre>
    <p class="muted small">Nazwa zespołu musi być identyczna jak w bazie (kolumna <code>teams.display_name</code>). Wartości z przecinkiem dziesiętnym są obsługiwane.</p>
</div>

<form method="post" action="/admin/import" enctype="multipart/form-data" class="form form-grid mt-2">
    <?= Csrf::field() ?>
    <label class="col-6">
        <span>Runda</span>
        <select name="round_id" required>
            <?php foreach (($rounds ?? []) as $r): ?>
                <option value="<?= (int)$r['id'] ?>"><?= $e($r['short_label']) ?> <?= $r['is_final'] ? '(FINAŁ)' : '' ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="col-6">
        <span>Dyscyplina</span>
        <select name="discipline" required>
            <option value="KPN">Karabin pneumatyczny (KPn)</option>
            <option value="PPN">Pistolet pneumatyczny (PPn)</option>
        </select>
    </label>
    <label class="col-12">
        <span>Plik CSV</span>
        <input type="file" name="csv" accept=".csv,text/csv" required>
    </label>
    <div class="col-12 actions-bar">
        <button class="btn btn-primary" type="submit">Importuj</button>
    </div>
</form>
