<?php
use App\Core\View;
$e = fn($v) => View::e($v);
$fmt = fn($v) => $v === null ? '—' : (fmod((float)$v,1.0)===0.0
    ? (string)(int)$v : str_replace('.', ',', rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.')));

// Pogrupowanie wyników: discipline -> round_code -> athlete_id -> score
$grouped = [];
$roundsSeen = [];
foreach ($scores as $row) {
    $disc = $row['discipline'];
    $rc   = $row['round_code'];
    $aid  = (int)$row['athlete_id'];
    $grouped[$disc]['name'] = $row['discipline_name'];
    $grouped[$disc]['rounds'][$rc] = ['code' => $rc, 'label' => $row['round_label'], 'num' => (int)$row['round_num']];
    $grouped[$disc]['athletes'][$aid] = [
        'first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'birth_year' => $row['birth_year'],
    ];
    $grouped[$disc]['scores'][$aid][$rc] = (float)$row['score'];
    $roundsSeen[$disc][$rc] = $row['round_label'];
}
?>
<section class="page-head"><div class="container">
    <p class="muted"><a href="/kluby">← Kluby</a></p>
    <h1><?= $e($club['name']) ?></h1>
    <p><?= $e($club['city']) ?><?= !empty($club['region']) ? ' · '.$e($club['region']) : '' ?></p>
</div></section>

<section class="container section">
    <?php if (empty($athletes)): ?>
        <div class="alert">Dla tego klubu nie dodano jeszcze zawodników. Skorzystaj z importu CSV lub uzupełnij dane w panelu admina.</div>
    <?php endif; ?>

    <?php if (!empty($grouped)): ?>
        <?php foreach ($grouped as $disc => $g): ?>
            <h2><?= $e($g['name']) ?></h2>
            <div class="table-wrap">
                <table class="results club-table">
                    <thead>
                    <tr>
                        <th>Zawodnik</th>
                        <th class="num">Rocznik</th>
                        <?php foreach ($g['rounds'] as $r): ?>
                            <th class="num"><?= $e($r['label']) ?></th>
                        <?php endforeach; ?>
                        <th class="num">Najlepszy</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($g['athletes'] as $aid => $a): $best = null; ?>
                        <tr>
                            <td><strong><?= $e($a['last_name']) ?> <?= $e($a['first_name']) ?></strong></td>
                            <td class="num"><?= $e($a['birth_year']) ?></td>
                            <?php foreach ($g['rounds'] as $r): $v = $g['scores'][$aid][$r['code']] ?? null;
                                if ($v !== null && ($best === null || $v > $best)) $best = $v; ?>
                                <td class="num"><?= $e($fmt($v)) ?></td>
                            <?php endforeach; ?>
                            <td class="num strong"><?= $e($fmt($best)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php elseif (!empty($athletes)): ?>
        <h2>Zawodnicy</h2>
        <div class="table-wrap">
            <table class="results">
                <thead><tr><th>Nazwisko i imię</th><th class="num">Rocznik</th></tr></thead>
                <tbody>
                <?php foreach ($athletes as $a): ?>
                    <tr><td><strong><?= $e($a['last_name']) ?> <?= $e($a['first_name']) ?></strong></td>
                        <td class="num"><?= $e($a['birth_year']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
