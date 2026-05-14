<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ResultRepository
{
    public function __construct(private PDO $pdo) {}

    public function disciplines(): array
    {
        return $this->pdo->query('SELECT * FROM disciplines ORDER BY sort ASC')->fetchAll();
    }

    /**
     * Ranking zespołów wg najwyższego wyniku z dotychczasowych rund.
     * @return array<int,array<string,mixed>>
     */
    public function standings(int $editionId, int $disciplineId): array
    {
        $sql = "
            SELECT
                t.id           AS team_id,
                t.display_name AS team,
                c.city         AS city,
                MAX(ts.score)  AS best_score,
                COUNT(ts.id)   AS rounds_played,
                (SELECT r.short_label
                 FROM team_scores ts2
                 JOIN rounds r ON r.id = ts2.round_id
                 WHERE ts2.team_id = t.id
                 ORDER BY ts2.score DESC LIMIT 1) AS best_round
            FROM teams t
            JOIN clubs c ON c.id = t.club_id
            LEFT JOIN team_scores ts ON ts.team_id = t.id
            WHERE t.edition_id = :e AND t.discipline_id = :d
            GROUP BY t.id
            ORDER BY best_score DESC, t.display_name ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':e' => $editionId, ':d' => $disciplineId]);
        $rows = $stmt->fetchAll();

        $place = 0;
        foreach ($rows as &$row) {
            $row['place'] = ++$place;
            $row['qualified'] = $place <= 8;
            $row['best_score'] = $row['best_score'] !== null ? (float)$row['best_score'] : null;
        }
        return $rows;
    }

    /**
     * Wszystkie wyniki w danej rundzie pogrupowane po dyscyplinie.
     */
    public function roundResults(int $roundId): array
    {
        $sql = "
            SELECT d.id AS discipline_id, d.code AS discipline_code, d.name AS discipline_name,
                   t.display_name AS team, c.city, ts.score
            FROM team_scores ts
            JOIN teams t      ON t.id = ts.team_id
            JOIN clubs c      ON c.id = t.club_id
            JOIN disciplines d ON d.id = t.discipline_id
            WHERE ts.round_id = :r
            ORDER BY d.sort, ts.score DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':r' => $roundId]);
        $grouped = [];
        foreach ($stmt->fetchAll() as $row) {
            $grouped[$row['discipline_code']]['name'] = $row['discipline_name'];
            $grouped[$row['discipline_code']]['rows'][] = $row;
        }
        return $grouped;
    }
}
