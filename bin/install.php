<?php
declare(strict_types=1);

/**
 * Instalator bazy: tworzy schemat i ładuje seed.
 * Uruchom: php bin/install.php
 */

require dirname(__DIR__) . '/src/Core/Bootstrap.php';

use App\Core\Bootstrap;
use App\Core\Database;

$config = Bootstrap::init();
$pdo = Database::pdo($config['db']);

$base = dirname(__DIR__);
$files = array_merge(
    glob($base . '/database/migrations/*.sql') ?: [],
    glob($base . '/database/seeds/*.sql') ?: []
);
sort($files, SORT_NATURAL);

foreach ($files as $file) {
    if (!is_file($file)) {
        fwrite(STDERR, "Brak pliku: $file\n");
        exit(1);
    }
    echo "→ Wykonuję: " . basename($file) . PHP_EOL;
    $sql = file_get_contents($file);
    foreach (splitStatements($sql) as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '') continue;
        try {
            $pdo->exec($stmt);
        } catch (\Throwable $e) {
            fwrite(STDERR, "Błąd SQL: " . $e->getMessage() . "\n--- statement ---\n" . substr($stmt, 0, 200) . "\n");
            exit(2);
        }
    }
}
echo "✓ Gotowe. Baza zainstalowana." . PHP_EOL;

/** Prymitywny splitter SQL po średnikach (ignoruje średniki w stringach). */
function splitStatements(string $sql): array
{
    $out = []; $buf = ''; $inStr = false; $strCh = '';
    $len = strlen($sql);
    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        if ($inStr) {
            $buf .= $c;
            if ($c === $strCh && $sql[$i-1] !== '\\') { $inStr = false; }
            continue;
        }
        if ($c === '"' || $c === "'") { $inStr = true; $strCh = $c; $buf .= $c; continue; }
        if ($c === ';') { $out[] = $buf; $buf = ''; continue; }
        $buf .= $c;
    }
    if (trim($buf) !== '') $out[] = $buf;
    return $out;
}
