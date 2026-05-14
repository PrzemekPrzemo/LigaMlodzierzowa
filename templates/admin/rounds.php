<?php use App\Core\View; use App\Core\Csrf; $e = fn($v) => View::e($v); ?>
<header class="admin-head">
    <h1>Rundy &amp; wyniki</h1>
    <a class="btn btn-ghost-d" href="/admin/import">Import CSV</a>
</header>

<?php if (empty($rounds)): ?>
    <div class="alert">Brak rund. Uruchom <code>php bin/install.php</code>.</div>
<?php else: ?>
    <div class="table-wrap">
        <table class="results">
            <thead>
            <tr><th>#</th><th>Runda</th><th>Miasto</th><th>Termin</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($rounds as $r): ?>
                <tr>
                    <td class="num"><?= $r['is_final'] ? 'FINAŁ' : (int)$r['number'] ?></td>
                    <td><strong><?= $e($r['label']) ?></strong><br><span class="muted small"><?= $e($r['short_label']) ?></span></td>
                    <td><?= $e($r['city']) ?></td>
                    <td><?= $e($r['starts_on'] ? date('d.m.Y', strtotime($r['starts_on'])) : '—') ?></td>
                    <td>
                        <form method="post" action="/admin/rundy/<?= (int)$r['id'] ?>/status" class="inline-form">
                            <?= Csrf::field() ?>
                            <select name="status" onchange="this.form.submit()">
                                <?php foreach (['planned'=>'zaplanowana','ongoing'=>'w trakcie','finished'=>'rozegrana'] as $k=>$v): ?>
                                    <option value="<?= $e($k) ?>" <?= $r['status']===$k?'selected':'' ?>><?= $e($v) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                    <td class="actions"><a class="btn btn-sm" href="/admin/rundy/<?= (int)$r['id'] ?>/wyniki">Wyniki</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
