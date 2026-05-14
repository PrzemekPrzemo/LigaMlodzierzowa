<?php
use App\Core\View;
use App\Core\Csrf;
$e = fn($v) => View::e($v);
$existing = [];
foreach (($scores ?? []) as $s) { $existing[(int)$s['team_id']] = $s; }
?>
<header class="admin-head">
    <h1><?= $e($title) ?></h1>
    <a class="btn btn-ghost-d" href="/admin/rundy">← Rundy</a>
</header>

<?php if (!$round): ?>
    <div class="alert">Brak rundy.</div>
<?php else: ?>
    <p class="muted">Wpisz lub zaktualizuj wyniki zespołów w tej rundzie. Wartości puste są ignorowane. Karabin — z dziesiętnymi (np. 1243,1), pistolet — bez dziesiętnych (np. 1105).</p>

    <form method="post" action="/admin/rundy/<?= (int)$round['id'] ?>/wyniki">
        <?= Csrf::field() ?>
        <?php foreach ($disciplines as $d): ?>
            <h2 class="mt-2"><?= $e($d['name']) ?></h2>
            <div class="table-wrap">
                <table class="results form-table">
                    <thead><tr><th>Zespół</th><th class="num">Wynik</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($teams[$d['code']] ?? []) as $t): $cur = $existing[(int)$t['id']] ?? null; ?>
                        <tr>
                            <td><?= $e($t['display_name']) ?></td>
                            <td class="num">
                                <input type="text" inputmode="decimal" name="score[<?= (int)$t['id'] ?>]"
                                       value="<?= $cur ? $e(rtrim(rtrim(number_format((float)$cur['score'], 2, ',', ''), '0'), ',')) : '' ?>"
                                       placeholder="—">
                            </td>
                            <td class="actions">
                                <?php if ($cur): ?>
                                <form method="post" action="/admin/wynik/<?= (int)$cur['id'] ?>/delete" class="inline-form"
                                      onsubmit="return confirm('Usunąć wynik?');">
                                    <?= Csrf::field() ?>
                                    <input type="hidden" name="round_id" value="<?= (int)$round['id'] ?>">
                                    <button class="btn btn-sm btn-danger" type="submit">Usuń</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($teams[$d['code']])): ?>
                        <tr><td colspan="3" class="muted">Brak zespołów dla tej dyscypliny.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <div class="actions-bar mt-2">
            <button class="btn btn-primary" type="submit">Zapisz wyniki</button>
        </div>
    </form>
<?php endif; ?>
