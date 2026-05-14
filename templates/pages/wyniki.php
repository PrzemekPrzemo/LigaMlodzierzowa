<?php
use App\Core\View;
$e = fn($v) => View::e($v);
?>
<section class="page-head">
    <div class="container">
        <h1>Wyniki — <?= $e($edition['title']) ?></h1>
        <p>Zespołowa klasyfikacja wg najwyższego wyniku z dotychczasowych rund. Top 8 awansuje do Finału.</p>
        <p class="muted">Stan na <?= $e(!empty($edition['state_as_of']) ? date('d.m.Y', strtotime($edition['state_as_of'])) : date('d.m.Y')) ?> · źródło: PZSS</p>
    </div>
</section>

<section class="container section">
    <?php if (empty($standings)): ?>
        <div class="alert">Brak danych. Zaimportuj wyniki z PDF lub uruchom seed bazy.</div>
    <?php else: ?>
        <div class="tabs" role="tablist">
            <?php $i=0; foreach ($standings as $code => $grp): $i++; ?>
                <button class="tab <?= $i===1 ? 'is-active' : '' ?>" role="tab" data-target="tab-<?= $e($code) ?>"><?= $e($grp['name']) ?></button>
            <?php endforeach; ?>
        </div>

        <?php $i=0; foreach ($standings as $code => $grp): $i++; ?>
            <div id="tab-<?= $e($code) ?>" class="tab-panel <?= $i===1 ? 'is-active' : '' ?>" role="tabpanel">
                <div class="table-wrap">
                    <table class="results">
                        <thead>
                        <tr>
                            <th class="num">#</th>
                            <th>Zespół</th>
                            <th>Miasto</th>
                            <th class="num">Najlepszy wynik</th>
                            <th class="num">Runda</th>
                            <th class="num">Startów</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($grp['rows'] as $row): ?>
                            <tr class="<?= $row['qualified'] ? 'is-qual' : '' ?>">
                                <td class="num"><?= (int)$row['place'] ?></td>
                                <td><strong><?= $e($row['team']) ?></strong></td>
                                <td><?= $e($row['city']) ?></td>
                                <td class="num"><?= $row['best_score'] !== null ? $e(number_format($row['best_score'], 1, ',', ' ')) : '—' ?></td>
                                <td class="num"><?= $e($row['best_round'] ?? '—') ?></td>
                                <td class="num"><?= (int)$row['rounds_played'] ?> / 4</td>
                                <td>
                                    <?php if ($row['qualified']): ?>
                                        <span class="tag tag-qual">Strefa Finału</span>
                                    <?php else: ?>
                                        <span class="tag tag-out">Poza top 8</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="muted small">O miejscu przy równych wynikach decyduje wyższy wynik najlepszego zawodnika zespołu z rundy branej pod uwagę do klasyfikacji.</p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
