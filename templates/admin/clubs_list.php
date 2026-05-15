<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); ?>
<header class="admin-head">
    <h1>Kluby</h1>
    <a class="btn btn-primary" href="/admin/kluby/new">+ Nowy klub</a>
</header>

<?php if (empty($rows)): ?>
    <div class="alert">Brak klubów w bazie. Uruchom <code>php bin/install.php</code> lub dodaj klub ręcznie.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="results">
            <thead>
            <tr><th>Nazwa</th><th>Miasto</th><th>Slug</th><th class="num">Zespoły</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><strong><?= $e($r['name']) ?></strong><?= !empty($r['short']) ? ' <span class="muted small">('.$e($r['short']).')</span>' : '' ?></td>
                    <td><?= $e($r['city']) ?></td>
                    <td class="muted small"><?= $e($r['slug']) ?></td>
                    <td class="num"><?= (int)$r['teams_count'] ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="/admin/kluby/<?= (int)$r['id'] ?>">Edytuj</a>
                        <a class="btn btn-sm btn-ghost-d" target="_blank" href="/klub/<?= $e($r['slug']) ?>">Podgląd ↗</a>
                        <form method="post" action="/admin/kluby/<?= (int)$r['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Usunąć klub <?= $e($r['name']) ?>? Spowoduje to także usunięcie wszystkich zespołów, zawodników i wyników tego klubu.');">
                            <?= Csrf::field() ?>
                            <button class="btn btn-sm btn-danger" type="submit">Usuń</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
