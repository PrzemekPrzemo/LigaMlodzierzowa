<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); ?>
<header class="admin-head">
    <h1>Zawodnicy</h1>
    <a class="btn btn-primary" href="/admin/zawodnicy/new">+ Nowy zawodnik</a>
</header>

<form method="get" action="/admin/zawodnicy" class="form form-grid filter-bar">
    <label class="col-6">
        <span>Szukaj (imię / nazwisko)</span>
        <input name="q" value="<?= $e($q ?? '') ?>" placeholder="np. Kowalski">
    </label>
    <label class="col-4">
        <span>Klub</span>
        <select name="klub">
            <option value="">— wszystkie —</option>
            <?php foreach (($clubs ?? []) as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (int)($clubId ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= $e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <div class="col-2 actions-bar"><button class="btn">Filtruj</button></div>
</form>

<?php if (empty($rows)): ?>
    <div class="alert">Brak zawodników. Dodaj pierwszego przyciskiem powyżej.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="results">
            <thead><tr><th>Nazwisko i imię</th><th>Klub</th><th>Dyscyplina</th><th class="num">Rocznik</th><th>Płeć</th><th></th></tr></thead>
            <tbody>
            <?php
            $discLabels = ['KPN'=>['Karabin','disc-kpn'], 'PPN'=>['Pistolet','disc-ppn'], 'BOTH'=>['Karabin + Pistolet','disc-both']];
            foreach ($rows as $a): ?>
                <tr>
                    <td><strong><?= $e($a['last_name']) ?> <?= $e($a['first_name']) ?></strong></td>
                    <td><?= $e($a['club_name'] ?? '—') ?></td>
                    <td>
                        <?php if (!empty($a['primary_discipline']) && isset($discLabels[$a['primary_discipline']])):
                            [$lbl,$cls] = $discLabels[$a['primary_discipline']]; ?>
                            <span class="disc-badge <?= $cls ?>"><?= $e($lbl) ?></span>
                        <?php else: ?>
                            <span class="muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="num"><?= $e($a['birth_year']) ?></td>
                    <td><?= $e($a['gender'] ?? '—') ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="/admin/zawodnicy/<?= (int)$a['id'] ?>">Edytuj</a>
                        <form method="post" action="/admin/zawodnicy/<?= (int)$a['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Usunąć zawodnika?');">
                            <?= Csrf::field() ?>
                            <button class="btn btn-sm btn-danger">Usuń</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
