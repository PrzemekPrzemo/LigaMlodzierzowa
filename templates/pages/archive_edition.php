<?php
use App\Core\View;
$e = fn($v) => View::e($v);
$fmt = fn($v) => $v === null ? '' : (fmod((float)$v,1.0)===0.0
    ? (string)(int)$v : str_replace('.', ',', rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.')));
?>
<section class="page-head"><div class="container">
    <p class="muted"><a href="/archiwum">← Archiwum</a></p>
    <h1><?= $e($archive_edition['title']) ?></h1>
    <p><?= $e($archive_edition['subtitle']) ?></p>
</div></section>

<section class="container section">
    <?php if (empty($tables)): ?>
        <div class="alert">Brak wyników w archiwum.</div>
    <?php else: ?>
        <div class="tabs" role="tablist">
            <?php $i=0; foreach ($tables as $code => $t): $i++; ?>
                <button class="tab <?= $i===1 ? 'is-active' : '' ?>" role="tab" data-target="ar-<?= $e($code) ?>"><?= $e($t['name']) ?></button>
            <?php endforeach; ?>
        </div>
        <?php $i=0; foreach ($tables as $code => $t): $i++;
            $rounds = $t['data']['rounds']; $rows = $t['data']['rows']; ?>
            <div id="ar-<?= $e($code) ?>" class="tab-panel <?= $i===1 ? 'is-active' : '' ?>">
                <div class="table-wrap">
                    <table class="results rounds-table">
                        <thead>
                        <tr>
                            <th class="num">Msc</th><th>Zespół</th>
                            <?php foreach ($rounds as $r): ?>
                                <th class="num"><?= $e($r['short_label']) ?></th>
                            <?php endforeach; ?>
                            <th class="num">Najlepszy</th>
                            <th class="num">Finał</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr class="<?= $row['qualified'] ? 'is-qual' : '' ?>">
                                <td class="num"><?= (int)$row['place'] ?></td>
                                <td><strong><?= $e($row['team']) ?></strong></td>
                                <?php foreach ($rounds as $r): $v = $row['per_round'][(int)$r['id']] ?? null;
                                    $isBest = ($v !== null && $row['best_score'] !== null && abs($v - $row['best_score']) < 0.001); ?>
                                    <td class="num <?= $isBest ? 'is-best' : '' ?>"><?= $e($fmt($v)) ?></td>
                                <?php endforeach; ?>
                                <td class="num strong"><?= $e($fmt($row['best_score'])) ?></td>
                                <td class="num"><?= $row['qualified'] ? '<span class="tag tag-qual">Q</span>' : '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
