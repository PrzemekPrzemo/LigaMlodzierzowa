<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ProgramRepository
{
    public function __construct(private PDO $pdo) {}

    public function eventsForRound(int $roundId): array
    {
        $s = $this->pdo->prepare(
            'SELECT * FROM round_events WHERE round_id = :r ORDER BY day_no, sort, time_start'
        );
        $s->execute([':r' => $roundId]);
        $grouped = [];
        foreach ($s->fetchAll() as $row) {
            $grouped[$row['day_no']]['label'] = $row['day_label'] ?: ('Dzień ' . $row['day_no']);
            $grouped[$row['day_no']]['rows'][] = $row;
        }
        ksort($grouped);
        return $grouped;
    }

    public function venueBySlug(string $slug): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM venues WHERE slug = :s');
        $s->execute([':s' => $slug]);
        return $s->fetch() ?: null;
    }
}
