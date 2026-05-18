<?php
use App\Core\View;
use App\Core\Csrf;
$e = fn($v) => View::e($v);
$existing = [];
foreach (($scores ?? []) as $s) { $existing[(int)$s['team_id']] = $s; }
$computed = $computed ?? [];
?>
<header class="admin-head">
    <h1><?= $e($title) ?></h1>
    <div class="actions-bar">
        <?php if ($round): ?>
            <form method="post" action="/admin/rundy/<?= (int)$round['id'] ?>/przelicz" class="inline-form"
                  onsubmit="return confirm('Przeliczyć wszystkie wyniki zespołowe z indywidualnych w tej rundzie? Zastąpi to ręczne wpisy dla klubów, które mają dane zawodników.');">
                <?= Csrf::field() ?>
                <button class="btn btn-ghost-d btn-sm" type="submit">⟳ Przelicz z zawodników</button>
            </form>
        <?php endif; ?>
        <a class="btn btn-ghost-d btn-sm" href="/admin/rundy">← Rundy</a>
    </div>
</header>

<?php if (!$round): ?>
    <div class="alert">Brak rundy.</div>
<?php else: ?>
    <div class="prose mt-1">
        <p class="muted small">
            Wpisz wyniki <strong>zespołowe</strong> w polach poniżej (np. <code>1243,1</code>).
            Pola <strong>puste są ignorowane</strong>.
            <br>
            🔵 <strong>Zespoły z wynikami indywidualnymi</strong> (zawodnicy wpisani w
            <code>/admin/zawodnicy</code>) mają wynik <strong>liczony automatycznie</strong> jako
            suma 3 najlepszych. Ręczne wpisy dla tych klubów zostaną nadpisane.
            <br>
            Karabin — z dziesiętnymi (np. 1243,1), pistolet — bez dziesiętnych (np. 1105).
        </p>
    </div>

    <form method="post" action="/admin/rundy/<?= (int)$round['id'] ?>/wyniki" class="mt-1">
        <?= Csrf::field() ?>
        <?php foreach ($disciplines as $d): ?>
            <h2 class="mt-2"><?= $e($d['name']) ?></h2>
            <div class="table-wrap">
                <table class="results form-table">
                    <thead><tr><th>Zespół</th><th class="num">Wynik zespołu</th><th>Źródło</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($teams[$d['code']] ?? []) as $t):
                        $cur = $existing[(int)$t['id']] ?? null;
                        $isComputed = isset($computed[(int)$t['id']]);
                    ?>
                        <tr class="<?= $isComputed ? 'tr-computed' : '' ?>">
                            <td>
                                <?= $e($t['display_name']) ?>
                                <?php if ($isComputed): ?>
                                    <span class="tag tag-computed" title="Wynik liczony automatycznie z zawodników">auto</span>
                                <?php endif; ?>
                            </td>
                            <td class="num">
                                <input type="text" inputmode="decimal" name="score[<?= (int)$t['id'] ?>]"
                                       value="<?= $cur ? $e(rtrim(rtrim(number_format((float)$cur['score'], 2, ',', ''), '0'), ',')) : '' ?>"
                                       placeholder="—"
                                       <?= $isComputed ? 'readonly title="Wynik liczony z zawodników — edytuj w /admin/zawodnicy"' : '' ?>>
                            </td>
                            <td class="muted small">
                                <?php if ($isComputed): ?>
                                    suma 3 zawodników
                                <?php elseif ($cur): ?>
                                    ręczny
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if ($cur && !$isComputed): ?>
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
                        <tr><td colspan="4" class="muted">Brak zespołów dla tej dyscypliny.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
        <div class="actions-bar mt-2">
            <button class="btn btn-primary" type="submit">Zapisz wyniki ręczne</button>
            <span class="muted small">(zespoły oznaczone „auto" pomijają ten zapis)</span>
        </div>
    </form>
<?php endif; ?>
