<?php
use App\Core\View;
$e = fn($v) => View::e($v);
$fmt = fn($v) => $v === null ? '' : (fmod((float)$v, 1.0) === 0.0
    ? (string)(int)$v
    : str_replace('.', ',', rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.')));
?>
<section class="page-head">
    <div class="container">
        <h1>Wyniki — <?= $e($edition['title']) ?></h1>
        <p>Klasyfikacja zespołowa. Do Finału kwalifikuje się 8 najlepszych zespołów (literka <strong>Q</strong>) wg najwyższego wyniku z dotychczasowych rund.</p>
        <p class="muted">Stan na <?= $e(!empty($edition['state_as_of']) ? date('d.m.Y', strtotime($edition['state_as_of'])) : date('d.m.Y')) ?> · źródło: PZSS</p>
    </div>
</section>

<section class="container section">
    <?php if (empty($tables)): ?>
        <div class="alert">Brak danych. Uruchom <code>php bin/install.php</code>.</div>
    <?php else: ?>
        <div class="view-switch" role="tablist" aria-label="Widok wyników">
            <button class="vs-btn is-active" data-view="rounds">Tabela rund</button>
            <button class="vs-btn"           data-view="ranking">Ranking sezonu</button>
            <a class="vs-link" target="_blank" rel="noopener" href="<?= $e($edition['results_pdf']) ?>">PDF PZSS ↗</a>
            <a class="vs-link" target="_blank" rel="noopener" href="/api/rounds-table.json">JSON ↗</a>
        </div>

        <!-- WIDOK: Tabela rund (PDF style) -->
        <div class="view view-rounds is-active">
            <div class="tabs" role="tablist">
                <?php $i=0; foreach ($tables as $code => $t): $i++; ?>
                    <button class="tab <?= $i===1 ? 'is-active' : '' ?>" role="tab" data-target="tr-<?= $e($code) ?>"><?= $e($t['name']) ?></button>
                <?php endforeach; ?>
            </div>
            <?php $i=0; foreach ($tables as $code => $t): $i++;
                $rounds = $t['data']['rounds']; $rows = $t['data']['rows']; ?>
                <div id="tr-<?= $e($code) ?>" class="tab-panel <?= $i===1 ? 'is-active' : '' ?>">
                    <div class="table-wrap">
                        <table class="results rounds-table">
                            <thead>
                            <tr>
                                <th class="num">Msc</th>
                                <th>Zespół</th>
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
                                    <td>
                                        <?php if (!empty($row['club_slug'])): ?>
                                            <a href="/klub/<?= $e($row['club_slug']) ?>"><strong><?= $e($row['team']) ?></strong></a>
                                        <?php else: ?>
                                            <strong><?= $e($row['team']) ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <?php foreach ($rounds as $r): $v = $row['per_round'][(int)$r['id']] ?? null;
                                        $isBest = ($v !== null && $row['best_score'] !== null && abs($v - $row['best_score']) < 0.001); ?>
                                        <td class="num <?= $isBest ? 'is-best' : '' ?>"><?= $e($fmt($v)) ?></td>
                                    <?php endforeach; ?>
                                    <td class="num strong"><?= $e($fmt($row['best_score'])) ?></td>
                                    <td class="num">
                                        <?php if ($row['qualified']): ?><span class="tag tag-qual">Q</span><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- WIDOK: Ranking sezonu -->
        <div class="view view-ranking">
            <div class="tabs" role="tablist">
                <?php $i=0; foreach ($standings as $code => $grp): $i++; ?>
                    <button class="tab <?= $i===1 ? 'is-active' : '' ?>" role="tab" data-target="rk-<?= $e($code) ?>"><?= $e($grp['name']) ?></button>
                <?php endforeach; ?>
            </div>
            <?php $i=0; foreach ($standings as $code => $grp): $i++; ?>
                <div id="rk-<?= $e($code) ?>" class="tab-panel <?= $i===1 ? 'is-active' : '' ?>">
                    <div class="table-wrap">
                        <table class="results">
                            <thead>
                            <tr><th class="num">#</th><th>Zespół</th><th>Miasto</th><th class="num">Najlepszy wynik</th><th class="num">Runda</th><th class="num">Startów</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($grp['rows'] as $row): ?>
                                <tr class="<?= $row['qualified'] ? 'is-qual' : '' ?>">
                                    <td class="num"><?= (int)$row['place'] ?></td>
                                    <td><strong><?= $e($row['team']) ?></strong></td>
                                    <td><?= $e($row['city']) ?></td>
                                    <td class="num"><?= $row['best_score'] !== null ? $e($fmt($row['best_score'])) : '—' ?></td>
                                    <td class="num"><?= $e($row['best_round'] ?? '—') ?></td>
                                    <td class="num"><?= (int)$row['rounds_played'] ?> / 4</td>
                                    <td><?= $row['qualified'] ? '<span class="tag tag-qual">Strefa Finału</span>' : '<span class="tag tag-out">Poza top 8</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
