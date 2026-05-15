<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); $c = $club; ?>
<header class="admin-head">
    <h1><?= $c ? 'Edycja klubu' : 'Nowy klub' ?></h1>
    <a class="btn btn-ghost-d" href="/admin/kluby">← Lista klubów</a>
</header>

<form method="post" action="/admin/kluby" class="form form-grid">
    <?= Csrf::field() ?>
    <?php if ($c): ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><?php endif; ?>
    <label class="col-8">
        <span>Nazwa pełna (np. ZAWISZA Bydgoszcz)</span>
        <input name="name" required value="<?= $e($c['name'] ?? '') ?>">
    </label>
    <label class="col-4">
        <span>Skrót (np. ZAWISZA)</span>
        <input name="short" value="<?= $e($c['short'] ?? '') ?>">
    </label>
    <label class="col-6">
        <span>Slug (pusty = wygeneruje się z nazwy)</span>
        <input name="slug" value="<?= $e($c['slug'] ?? '') ?>" placeholder="np. zawisza-bydgoszcz">
    </label>
    <label class="col-3">
        <span>Miasto</span>
        <input name="city" value="<?= $e($c['city'] ?? '') ?>">
    </label>
    <label class="col-3">
        <span>Region / województwo</span>
        <input name="region" value="<?= $e($c['region'] ?? '') ?>">
    </label>
    <label class="col-6">
        <span>Logo (URL)</span>
        <input name="logo" value="<?= $e($c['logo'] ?? '') ?>" placeholder="/assets/img/clubs/zawisza.svg">
    </label>
    <label class="col-6">
        <span>Strona klubu</span>
        <input name="website" value="<?= $e($c['website'] ?? '') ?>" placeholder="https://...">
    </label>
    <div class="col-12 actions-bar">
        <button class="btn btn-primary" type="submit"><?= $c ? 'Zapisz zmiany' : 'Utwórz klub' ?></button>
        <a class="btn btn-ghost-d" href="/admin/kluby">Anuluj</a>
    </div>
</form>

<?php if ($c && !empty($disciplines)): ?>
    <hr class="adm-sep">
    <h2>Zespoły w edycji <?= $e($edition['year']) ?></h2>
    <p class="muted small">Każdy klub może mieć po jednym zespole w każdej dyscyplinie. Dodanie zespołu pozwala wpisywać wyniki.</p>

    <div class="team-toggle">
        <?php foreach ($disciplines as $d):
            $existing = null;
            foreach ($teams as $t) { if ((int)$t['discipline_id'] === (int)$d['id']) { $existing = $t; break; } }
        ?>
            <div class="tt-row">
                <div>
                    <strong><?= $e($d['name']) ?></strong>
                    <span class="muted small"><?= $e($d['short']) ?></span>
                </div>
                <?php if ($existing): ?>
                    <span class="tag tag-finished">aktywny</span>
                    <form method="post" action="/admin/kluby/<?= (int)$c['id'] ?>/zespol" class="inline-form" onsubmit="return confirm('Usunąć zespół z edycji? Wyniki tego zespołu zostaną usunięte.');">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="team_id" value="<?= (int)$existing['id'] ?>">
                        <button class="btn btn-sm btn-ghost-d">Usuń</button>
                    </form>
                <?php else: ?>
                    <span class="tag tag-out">brak</span>
                    <form method="post" action="/admin/kluby/<?= (int)$c['id'] ?>/zespol" class="inline-form">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="discipline_id" value="<?= (int)$d['id'] ?>">
                        <button class="btn btn-sm">Dodaj zespół</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <hr class="adm-sep">
    <header class="admin-head">
        <h2>Zawodnicy klubu</h2>
        <a class="btn btn-primary btn-sm" href="/admin/zawodnicy/new?klub=<?= (int)$c['id'] ?>">+ Dodaj zawodnika</a>
    </header>
    <?php if (empty($athletes)): ?>
        <div class="alert">Klub nie ma jeszcze zawodników.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table class="results">
                <thead><tr><th>Nazwisko i imię</th><th class="num">Rocznik</th><th>Płeć</th><th>Licencja</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($athletes as $a): ?>
                    <tr>
                        <td><strong><?= $e($a['last_name']) ?> <?= $e($a['first_name']) ?></strong></td>
                        <td class="num"><?= $e($a['birth_year']) ?></td>
                        <td><?= $e($a['gender'] ?? '—') ?></td>
                        <td class="muted small"><?= $e($a['license_no'] ?? '—') ?></td>
                        <td class="actions"><a class="btn btn-sm" href="/admin/zawodnicy/<?= (int)$a['id'] ?>">Edytuj</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
