<?php
use App\Core\View;
use App\Core\Csrf;
use App\Repository\MediaRepository;
$e = fn($v) => View::e($v);
$cats = MediaRepository::CATEGORIES;
?>
<header class="admin-head">
    <h1>Sponsorzy i partnerzy</h1>
    <a class="btn btn-primary" href="/admin/sponsorzy/new">+ Nowy partner</a>
</header>

<?php if (empty($rows)): ?>
    <div class="alert">Brak partnerów. Dodaj pierwszego przyciskiem powyżej.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="results">
            <thead>
            <tr><th>Nazwa</th><th>Kategoria</th><th>Tier</th><th>Zakres</th><th>Edycja</th><th class="num">Sort</th><th>Widoczny</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r):
                $catLabel = $cats[$r['category']][0] ?? $r['category'];
                $catCls   = $cats[$r['category']][1] ?? '';
            ?>
                <tr>
                    <td>
                        <?php if (!empty($r['logo'])): ?>
                            <img src="<?= $e($r['logo']) ?>" alt="" style="height:24px;vertical-align:middle;margin-right:.5rem">
                        <?php endif; ?>
                        <strong><?= $e($r['name']) ?></strong>
                        <?php if (!empty($r['url'])): ?> · <a href="<?= $e($r['url']) ?>" target="_blank" rel="noopener" class="muted small">www ↗</a><?php endif; ?>
                    </td>
                    <td><span class="cat-badge <?= $e($catCls) ?>"><?= $e($catLabel) ?></span></td>
                    <td class="muted small"><?= $e($r['tier']) ?></td>
                    <td class="muted small"><?= $e($r['scope']) ?></td>
                    <td class="muted small"><?= $r['year'] ? (int)$r['year'] : 'wszystkie' ?></td>
                    <td class="num"><?= (int)$r['sort'] ?></td>
                    <td><?= !empty($r['is_visible']) ? '✓' : '<span class="muted">—</span>' ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="/admin/sponsorzy/<?= (int)$r['id'] ?>">Edytuj</a>
                        <form method="post" action="/admin/sponsorzy/<?= (int)$r['id'] ?>/delete" class="inline-form" onsubmit="return confirm('Usunąć partnera <?= $e($r['name']) ?>?');">
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
