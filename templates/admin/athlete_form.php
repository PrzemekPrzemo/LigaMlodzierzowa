<?php
use App\Core\View;
use App\Core\Csrf;
$e = fn($v) => View::e($v);
$a = $athlete;
$preselect = (int)($preselect_club ?? 0);
$fmt = fn($v) => $v === null ? '' : (fmod((float)$v,1.0)===0.0
    ? (string)(int)$v : str_replace('.', ',', rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.')));
?>
<header class="admin-head">
    <h1><?= $a ? 'Edycja zawodnika' : 'Nowy zawodnik' ?></h1>
    <a class="btn btn-ghost-d" href="/admin/zawodnicy">← Lista zawodników</a>
</header>

<form method="post" action="/admin/zawodnicy" class="form form-grid">
    <?= Csrf::field() ?>
    <?php if ($a): ?><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><?php endif; ?>
    <label class="col-6">
        <span>Imię</span>
        <input name="first_name" required value="<?= $e($a['first_name'] ?? '') ?>">
    </label>
    <label class="col-6">
        <span>Nazwisko</span>
        <input name="last_name" required value="<?= $e($a['last_name'] ?? '') ?>">
    </label>
    <label class="col-4">
        <span>Klub</span>
        <select name="club_id">
            <option value="">— bez klubu —</option>
            <?php foreach (($clubs ?? []) as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (int)($a['club_id'] ?? $preselect) === (int)$c['id'] ? 'selected' : '' ?>><?= $e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="col-3">
        <span>Rocznik</span>
        <input name="birth_year" type="number" min="1990" max="2020" value="<?= $e($a['birth_year'] ?? '') ?>">
    </label>
    <label class="col-2">
        <span>Płeć</span>
        <select name="gender">
            <option value="" <?= empty($a['gender']) ? 'selected' : '' ?>>—</option>
            <option value="M" <?= ($a['gender'] ?? '') === 'M' ? 'selected' : '' ?>>M</option>
            <option value="K" <?= ($a['gender'] ?? '') === 'K' ? 'selected' : '' ?>>K</option>
        </select>
    </label>
    <fieldset class="col-4 discipline-radio">
        <legend>Dyscyplina (główna)</legend>
        <?php $pd = $a['primary_discipline'] ?? ''; ?>
        <label><input type="radio" name="primary_discipline" value="KPN"  <?= $pd === 'KPN'  ? 'checked' : '' ?>> Karabin <small>KPn</small></label>
        <label><input type="radio" name="primary_discipline" value="PPN"  <?= $pd === 'PPN'  ? 'checked' : '' ?>> Pistolet <small>PPn</small></label>
        <label><input type="radio" name="primary_discipline" value="BOTH" <?= $pd === 'BOTH' ? 'checked' : '' ?>> Obie</label>
        <label><input type="radio" name="primary_discipline" value=""     <?= $pd === ''     ? 'checked' : '' ?>> —</label>
    </fieldset>
    <label class="col-3">
        <span>Nr licencji</span>
        <input name="license_no" value="<?= $e($a['license_no'] ?? '') ?>">
    </label>
    <label class="col-6">
        <span>Slug (pusty = automatyczny)</span>
        <input name="slug" value="<?= $e($a['slug'] ?? '') ?>">
    </label>
    <div class="col-12 actions-bar">
        <button class="btn btn-primary" type="submit"><?= $a ? 'Zapisz zmiany' : 'Utwórz zawodnika' ?></button>
        <a class="btn btn-ghost-d" href="/admin/zawodnicy">Anuluj</a>
    </div>
</form>

<?php if ($a): ?>
    <hr class="adm-sep">
    <header class="admin-head">
        <h2>Wyniki indywidualne</h2>
    </header>

    <form method="post" action="/admin/zawodnicy/<?= (int)$a['id'] ?>/wynik" class="form form-grid">
        <?= Csrf::field() ?>
        <label class="col-4">
            <span>Runda</span>
            <select name="round_id" required>
                <?php foreach (($rounds ?? []) as $r): ?>
                    <option value="<?= (int)$r['id'] ?>"><?= $e($r['short_label']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="col-4">
            <span>Dyscyplina</span>
            <select name="discipline_id" required>
                <?php foreach (($disciplines ?? []) as $d): ?>
                    <option value="<?= (int)$d['id'] ?>"><?= $e($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="col-3">
            <span>Wynik (np. 415,4)</span>
            <input name="score" type="text" inputmode="decimal" required placeholder="np. 415,4">
        </label>
        <div class="col-1 actions-bar"><button class="btn btn-primary">Dodaj</button></div>
    </form>

    <?php if (!empty($scores)): ?>
        <div class="table-wrap mt-1">
            <table class="results">
                <thead><tr><th>Dyscyplina</th><th>Runda</th><th class="num">Wynik</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($scores as $s): ?>
                    <tr>
                        <td><?= $e($s['discipline_name']) ?></td>
                        <td><?= $e($s['short_label']) ?></td>
                        <td class="num"><?= $e($fmt($s['score'])) ?></td>
                        <td class="actions">
                            <form method="post" action="/admin/zawodnicy/<?= (int)$a['id'] ?>/wynik/<?= (int)$s['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Usunąć wynik?');">
                                <?= Csrf::field() ?>
                                <button class="btn btn-sm btn-danger">Usuń</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="muted">Brak wpisanych wyników indywidualnych.</p>
    <?php endif; ?>
<?php endif; ?>
