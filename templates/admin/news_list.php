<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); ?>
<header class="admin-head">
    <h1>Aktualności</h1>
    <a class="btn btn-primary" href="/admin/news/new">+ Nowy wpis</a>
</header>

<?php if (empty($rows)): ?>
    <div class="alert">Brak wpisów.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="results">
            <thead>
            <tr><th>Tytuł</th><th>Slug</th><th>Data</th><th class="num">Przypięty</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><strong><?= $e($r['title']) ?></strong><br><span class="muted small"><?= $e($r['lead']) ?></span></td>
                    <td class="muted small"><?= $e($r['slug']) ?></td>
                    <td class="num"><?= $e(date('d.m.Y', strtotime($r['published_at']))) ?></td>
                    <td class="num"><?= !empty($r['is_pinned']) ? '★' : '' ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="/admin/news/<?= (int)$r['id'] ?>">Edytuj</a>
                        <form method="post" action="/admin/news/<?= (int)$r['id'] ?>/delete" onsubmit="return confirm('Usunąć wpis?');" style="display:inline">
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
