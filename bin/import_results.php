<?php
declare(strict_types=1);

/**
 * Import wyników rundy z pliku CSV.
 *
 * Użycie:
 *   php bin/import_results.php <round_code> <discipline_code> <plik.csv>
 *
 * Przykład:
 *   php bin/import_results.php PB KPN data/runda2-karabin.csv
 *
 * Format CSV: NAZWA_ZESPOLU,WYNIK   (nagłówek opcjonalny, przecinek dziesiętny obsługiwany)
 */

require dirname(__DIR__) . '/src/Core/Bootstrap.php';

use App\Core\Bootstrap;
use App\Core\Database;

$config = Bootstrap::init();
$pdo = Database::pdo($config['db']);

if ($argc < 4) {
    fwrite(STDERR, "Użycie: php bin/import_results.php <round_code> <discipline_code> <plik.csv>\n");
    exit(1);
}
[$_, $roundCode, $disciplineCode, $csvPath] = $argv;
if (!is_file($csvPath)) { fwrite(STDERR, "Brak pliku: $csvPath\n"); exit(2); }

$ed = $pdo->query('SELECT id FROM editions WHERE is_active = 1 ORDER BY year DESC LIMIT 1')->fetch();
if (!$ed) { fwrite(STDERR, "Brak aktywnej edycji.\n"); exit(3); }
$editionId = (int)$ed['id'];

$round = $pdo->prepare('SELECT id FROM rounds WHERE edition_id = :e AND code = :c');
$round->execute([':e' => $editionId, ':c' => $roundCode]);
$round = $round->fetch();
if (!$round) { fwrite(STDERR, "Brak rundy o kodzie: $roundCode\n"); exit(4); }

$disc = $pdo->prepare('SELECT id FROM disciplines WHERE code = :c');
$disc->execute([':c' => $disciplineCode]);
$disc = $disc->fetch();
if (!$disc) { fwrite(STDERR, "Brak dyscypliny o kodzie: $disciplineCode\n"); exit(5); }

$teams = $pdo->prepare('SELECT id, display_name FROM teams WHERE edition_id = :e AND discipline_id = :d');
$teams->execute([':e' => $editionId, ':d' => (int)$disc['id']]);
$map = [];
foreach ($teams->fetchAll() as $t) { $map[mb_strtolower(trim($t['display_name']))] = (int)$t['id']; }

$ins = $pdo->prepare(
    'INSERT INTO team_scores (team_id, round_id, score) VALUES (:t, :r, :s)
     ON DUPLICATE KEY UPDATE score = VALUES(score)'
);

$h = fopen($csvPath, 'rb');
$rows = 0; $ok = 0; $skipped = [];
while (($r = fgetcsv($h, 0, ',', '"', '\\')) !== false) {
    $rows++;
    $name  = isset($r[0]) ? mb_strtolower(trim((string)$r[0])) : '';
    $score = isset($r[1]) ? str_replace(',', '.', trim((string)$r[1])) : '';
    if ($rows === 1 && !is_numeric($score)) { continue; }
    if ($name === '' || !is_numeric($score)) { $skipped[] = $r[0] ?? '(pusty)'; continue; }
    $tid = $map[$name] ?? null;
    if (!$tid) { $skipped[] = $r[0]; continue; }
    $ins->execute([':t' => $tid, ':r' => (int)$round['id'], ':s' => (float)$score]);
    $ok++;
}
fclose($h);

echo "✓ Zaimportowano: $ok, pominięto: " . count($skipped) . PHP_EOL;
if ($skipped) { echo "Pominięte: " . implode(', ', $skipped) . PHP_EOL; }
